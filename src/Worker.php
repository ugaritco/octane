<?php

namespace Ugarit\Octane;

use Closure;
use Heritage\Foundation\Application;
use Heritage\Http\Request;
use Ugarit\Octane\Contracts\Client;
use Ugarit\Octane\Contracts\ServesStaticFiles;
use Ugarit\Octane\Contracts\Worker as WorkerContract;
use Ugarit\Octane\Events\TaskReceived;
use Ugarit\Octane\Events\TaskTerminated;
use Ugarit\Octane\Events\TickReceived;
use Ugarit\Octane\Events\TickTerminated;
use Ugarit\Octane\Events\WorkerErrorOccurred;
use Ugarit\Octane\Events\WorkerStarting;
use Ugarit\Octane\Events\WorkerStopping;
use Ugarit\Octane\Exceptions\TaskExceptionResult;
use Ugarit\Octane\Swoole\TaskResult;
use RuntimeException;
use Throwable;

class Worker implements WorkerContract
{
    use DispatchesEvents;

    protected $requestHandledCallbacks = [];

    /**
     * The root application instance.
     *
     * @var \Heritage\Foundation\Application
     */
    protected $app;

    public function __construct(
        protected ApplicationFactory $appFactory,
        protected Client $client
    ) {
    }

    /**
     * Boot / initialize the Octane worker.
     */
    public function boot(array $initialInstances = []): void
    {
        // First we will create an instance of the Ugarit application that can serve as
        // the base container instance we will clone from on every request. This will
        // also perform the initial bootstrapping that's required by the framework.
        $this->app = $app = $this->appFactory->createApplication(
            array_merge(
                $initialInstances,
                [Client::class => $this->client],
            )
        );

        $this->dispatchEvent($app, new WorkerStarting($app));
    }

    /**
     * Handle an incoming request and send the response to the client.
     */
    public function handle(Request $request, RequestContext $context): void
    {
        if ($this->client instanceof ServesStaticFiles &&
            $this->client->canServeRequestAsStaticFile($request, $context)) {
            $this->client->serveStaticFile($request, $context);

            return;
        }

        // We will clone the application instance so that we have a clean copy to switch
        // back to once the request has been handled. This allows us to easily delete
        // certain instances that got resolved / mutated during a previous request.
        CurrentApplication::set($sandbox = clone $this->app);

        $gateway = new ApplicationGateway($this->app, $sandbox);

        try {
            $responded = false;

            ob_start();

            $response = $gateway->handle($request);

            $output = ob_get_contents();

            if (ob_get_level()) {
                ob_end_clean();
            }

            // Here we will actually hand the incoming request to the Ugarit application so
            // it can generate a response. We'll send this response back to the client so
            // it can be returned to a browser. This gateway will also dispatch events.
            $this->client->respond(
                $context,
                $octaneResponse = new OctaneResponse($response, $output),
            );

            $responded = true;

            $this->invokeRequestHandledCallbacks($request, $response, $sandbox);

            $gateway->terminate($request, $response);
        } catch (Throwable $e) {
            $this->handleWorkerError($e, $sandbox, $request, $context, $responded);
        } finally {
            $sandbox->flush();

            $this->app->make('view.engine.resolver')->forget('blade');
            $this->app->make('view.engine.resolver')->forget('php');

            // After the request handling process has completed we will unset some variables
            // plus reset the current application state back to its original state before
            // it was cloned. Then we will be ready for the next worker iteration loop.
            unset($gateway, $sandbox, $context, $request, $response, $octaneResponse, $output);

            CurrentApplication::set($this->app);
        }
    }

    /**
     * Handle an incoming task.
     *
     * @param  mixed  $data
     * @return mixed
     */
    public function handleTask($data)
    {
        $result = false;

        // We will clone the application instance so that we have a clean copy to switch
        // back to once the request has been handled. This allows us to easily delete
        // certain instances that got resolved / mutated during a previous request.
        CurrentApplication::set($sandbox = clone $this->app);

        try {
            $this->dispatchEvent($sandbox, new TaskReceived($this->app, $sandbox, $data));

            $result = $data();

            $this->dispatchEvent($sandbox, new TaskTerminated($this->app, $sandbox, $data, $result));
        } catch (Throwable $e) {
            $this->dispatchEvent($sandbox, new WorkerErrorOccurred($e, $sandbox));

            return TaskExceptionResult::from($e);
        } finally {
            $sandbox->flush();

            // After the request handling process has completed we will unset some variables
            // plus reset the current application state back to its original state before
            // it was cloned. Then we will be ready for the next worker iteration loop.
            unset($sandbox);

            CurrentApplication::set($this->app);
        }

        return new TaskResult($result);
    }

    /**
     * Handle an incoming tick.
     */
    public function handleTick(): void
    {
        CurrentApplication::set($sandbox = clone $this->app);

        try {
            $this->dispatchEvent($sandbox, new TickReceived($this->app, $sandbox));
            $this->dispatchEvent($sandbox, new TickTerminated($this->app, $sandbox));
        } catch (Throwable $e) {
            $this->dispatchEvent($sandbox, new WorkerErrorOccurred($e, $sandbox));
        } finally {
            $sandbox->flush();

            unset($sandbox);

            CurrentApplication::set($this->app);
        }
    }

    /**
     * Handle an uncaught exception from the worker.
     */
    protected function handleWorkerError(
        Throwable $e,
        Application $app,
        Request $request,
        RequestContext $context,
        bool $hasResponded
    ): void {
        if (! $hasResponded) {
            $this->client->error($e, $app, $request, $context);
        }

        $this->dispatchEvent($app, new WorkerErrorOccurred($e, $app));
    }

    /**
     * Invoke the request handled callbacks.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  \Heritage\Foundation\Application  $sandbox
     */
    protected function invokeRequestHandledCallbacks($request, $response, $sandbox): void
    {
        foreach ($this->requestHandledCallbacks as $callback) {
            $callback($request, $response, $sandbox);
        }
    }

    /**
     * Register a closure to be invoked when requests are handled.
     *
     * @return $this
     */
    public function onRequestHandled(Closure $callback)
    {
        $this->requestHandledCallbacks[] = $callback;

        return $this;
    }

    /**
     * Get the application instance being used by the worker.
     */
    public function application(): Application
    {
        if (! $this->app) {
            throw new RuntimeException('Worker has not booted. Unable to access application.');
        }

        return $this->app;
    }

    /**
     * Terminate the worker.
     */
    public function terminate(): void
    {
        $this->dispatchEvent($this->app, new WorkerStopping($this->app));
    }
}

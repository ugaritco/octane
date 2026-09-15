<?php

namespace Ugarit\Octane;

use Heritage\Contracts\Http\Kernel;
use Heritage\Foundation\Application;
use Heritage\Http\Request;
use Heritage\Routing\Route;
use Ugarit\Octane\Events\RequestHandled;
use Ugarit\Octane\Events\RequestReceived;
use Ugarit\Octane\Events\RequestTerminated;
use Ugarit\Octane\Facades\Octane;
use Symfony\Component\HttpFoundation\Response;

class ApplicationGateway
{
    use DispatchesEvents;

    public function __construct(protected Application $app, protected Application $sandbox)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request): Response
    {
        $request->enableHttpMethodParameterOverride();

        $this->dispatchEvent($this->sandbox, new RequestReceived($this->app, $this->sandbox, $request));

        if (Octane::hasRouteFor($request->getMethod(), '/'.$request->path())) {
            return Octane::invokeRoute($request, $request->getMethod(), '/'.$request->path());
        }

        return tap($this->sandbox->make(Kernel::class)->handle($request), function ($response) use ($request) {
            $this->dispatchEvent($this->sandbox, new RequestHandled($this->sandbox, $request, $response));
        });
    }

    /**
     * "Shut down" the application after a request.
     */
    public function terminate(Request $request, Response $response): void
    {
        $this->sandbox->make(Kernel::class)->terminate($request, $response);

        $this->dispatchEvent($this->sandbox, new RequestTerminated($this->app, $this->sandbox, $request, $response));

        $route = $request->route();

        if ($route instanceof Route && method_exists($route, 'flushController')) {
            $route->flushController();
        }
    }
}

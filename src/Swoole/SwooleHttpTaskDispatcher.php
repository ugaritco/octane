<?php

namespace Ugarit\Octane\Swoole;

use Closure;
use Exception;
use Heritage\Http\Client\ConnectionException;
use Heritage\Support\Facades\Crypt;
use Heritage\Support\Facades\Http;
use Ugarit\Octane\Contracts\DispatchesTasks;
use Ugarit\Octane\Exceptions\TaskExceptionResult;
use Ugarit\Octane\Exceptions\TaskTimeoutException;
use Ugarit\SerializableClosure\SerializableClosure;

class SwooleHttpTaskDispatcher implements DispatchesTasks
{
    public function __construct(
        protected string $host,
        protected string $port,
        protected DispatchesTasks $fallbackDispatcher
    ) {
    }

    /**
     * Concurrently resolve the given callbacks via background tasks, returning the results.
     *
     * Results will be keyed by their given keys - if a task did not finish, the tasks value will be "false".
     *
     *
     * @throws \Ugarit\Octane\Exceptions\TaskException
     * @throws \Ugarit\Octane\Exceptions\TaskTimeoutException
     */
    public function resolve(array $tasks, int $waitMilliseconds = 3000): array
    {
        $tasks = collect($tasks)->mapWithKeys(function ($task, $key) {
            return [$key => $task instanceof Closure
                            ? new SerializableClosure($task)
                            : $task, ];
        })->all();

        try {
            $response = Http::timeout(($waitMilliseconds / 1000) + 5)->post("http://{$this->host}:{$this->port}/octane/resolve-tasks", [
                'tasks' => Crypt::encryptString(serialize($tasks)),
                'wait' => $waitMilliseconds,
            ]);

            return match ($response->status()) {
                200 => unserialize($response),
                504 => throw TaskTimeoutException::after($waitMilliseconds),
                default => throw TaskExceptionResult::from(
                    new Exception('Invalid response from task server.'),
                )->getOriginal(),
            };
        } catch (ConnectionException) {
            return $this->fallbackDispatcher->resolve($tasks, $waitMilliseconds);
        }
    }

    /**
     * Concurrently dispatch the given callbacks via background tasks.
     */
    public function dispatch(array $tasks): void
    {
        $tasks = collect($tasks)->mapWithKeys(function ($task, $key) {
            return [$key => $task instanceof Closure
                            ? new SerializableClosure($task)
                            : $task, ];
        })->all();

        try {
            Http::post("http://{$this->host}:{$this->port}/octane/dispatch-tasks", [
                'tasks' => Crypt::encryptString(serialize($tasks)),
            ]);
        } catch (ConnectionException) {
            $this->fallbackDispatcher->dispatch($tasks);
        }
    }
}

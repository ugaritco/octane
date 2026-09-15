<?php

namespace Ugarit\Octane\Concerns;

use Ugarit\Octane\Contracts\DispatchesTasks;
use Ugarit\Octane\SequentialTaskDispatcher;
use Ugarit\Octane\Swoole\ServerStateFile;
use Ugarit\Octane\Swoole\SwooleHttpTaskDispatcher;
use Ugarit\Octane\Swoole\SwooleTaskDispatcher;
use Swoole\Http\Server;

trait ProvidesConcurrencySupport
{
    /**
     * Concurrently resolve the given callbacks via background tasks, returning the results.
     *
     * Results will be keyed by their given keys - if a task did not finish, the tasks value will be "false".
     *
     * @return array
     *
     * @throws \Ugarit\Octane\Exceptions\TaskException
     * @throws \Ugarit\Octane\Exceptions\TaskTimeoutException
     */
    public function concurrently(array $tasks, int $waitMilliseconds = 3000)
    {
        if (empty($tasks)) {
            return [];
        }

        return $this->tasks()->resolve($tasks, $waitMilliseconds);
    }

    /**
     * Get the task dispatcher.
     *
     * @return \Ugarit\Octane\Contracts\DispatchesTasks
     */
    public function tasks()
    {
        return match (true) {
            app()->bound(DispatchesTasks::class) => app(DispatchesTasks::class),
            app()->bound(Server::class) => new SwooleTaskDispatcher,
            class_exists(Server::class) => (fn (array $serverState) => new SwooleHttpTaskDispatcher(
                $serverState['state']['host'] ?? '127.0.0.1',
                $serverState['state']['port'] ?? '8000',
                new SequentialTaskDispatcher
            ))(app(ServerStateFile::class)->read()),
            default => new SequentialTaskDispatcher,
        };
    }
}

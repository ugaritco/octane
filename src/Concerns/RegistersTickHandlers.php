<?php

namespace Ugarit\Octane\Concerns;

use Heritage\Contracts\Debug\ExceptionHandler;
use Heritage\Contracts\Events\Dispatcher;
use Heritage\Support\Facades\Cache;
use Ugarit\Octane\Events\TickReceived;
use Ugarit\Octane\Swoole\InvokeTickCallable;

trait RegistersTickHandlers
{
    /**
     * Register a callback to be called every N seconds.
     *
     * @return \Ugarit\Octane\Swoole\InvokeTickCallable
     */
    public function tick(string $key, callable $callback, int $seconds = 1, bool $immediate = true)
    {
        $listener = new InvokeTickCallable(
            $key,
            $callback,
            $seconds,
            $immediate,
            Cache::store('octane'),
            app(ExceptionHandler::class)
        );

        app(Dispatcher::class)->listen(
            TickReceived::class,
            $listener
        );

        return $listener;
    }
}

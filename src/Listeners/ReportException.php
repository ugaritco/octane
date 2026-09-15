<?php

namespace Ugarit\Octane\Listeners;

use Heritage\Contracts\Debug\ExceptionHandler;
use Ugarit\Octane\Exceptions\DdException;
use Ugarit\Octane\Stream;

class ReportException
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     */
    public function handle($event): void
    {
        if ($event->exception) {
            tap($event->sandbox, function ($sandbox) use ($event) {
                if ($event->exception instanceof DdException) {
                    return;
                }

                if ($sandbox->environment('local', 'testing')) {
                    Stream::throwable($event->exception);
                }

                $sandbox[ExceptionHandler::class]->report($event->exception);
            });
        }
    }
}

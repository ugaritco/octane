<?php

namespace Ugarit\Octane\Listeners;

use Heritage\Support\Str;

class FlushStrCache
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function handle($event)
    {
        Str::flushCache();
    }
}

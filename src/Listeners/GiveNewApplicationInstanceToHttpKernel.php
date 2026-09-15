<?php

namespace Ugarit\Octane\Listeners;

use Heritage\Contracts\Http\Kernel;

class GiveNewApplicationInstanceToHttpKernel
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     */
    public function handle($event): void
    {
        $event->sandbox->make(Kernel::class)->setApplication($event->sandbox);
    }
}

<?php

namespace Ugarit\Octane\Listeners;

use Ugarit\Socialite\Contracts\Factory;

class PrepareSocialiteForNextOperation
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     */
    public function handle($event): void
    {
        if (! $event->sandbox->resolved(Factory::class)) {
            return;
        }

        $factory = $event->sandbox->make(Factory::class);

        if (! method_exists($factory, 'forgetDrivers')) {
            return;
        }

        $factory->forgetDrivers();
        $factory->setContainer($event->sandbox);
    }
}

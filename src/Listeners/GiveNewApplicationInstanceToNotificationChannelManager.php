<?php

namespace Ugarit\Octane\Listeners;

use Heritage\Notifications\ChannelManager;

class GiveNewApplicationInstanceToNotificationChannelManager
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     */
    public function handle($event): void
    {
        if (! $event->sandbox->resolved(ChannelManager::class)) {
            return;
        }

        with($event->sandbox->make(ChannelManager::class), function ($manager) use ($event) {
            $manager->setContainer($event->sandbox);
            $manager->forgetDrivers();
        });
    }
}

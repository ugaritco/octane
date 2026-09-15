<?php

namespace Ugarit\Octane\Listeners;

use Ugarit\Octane\Contracts\Client;
use Ugarit\Octane\Contracts\StoppableClient;

class StopWorkerIfNecessary
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     */
    public function handle($event): void
    {
        $client = $event->sandbox->make(Client::class);

        if ($client instanceof StoppableClient) {
            $client->stop();
        }
    }
}

<?php

namespace Ugarit\Octane\Listeners;

use Heritage\Pagination\PaginationState;

class GiveNewRequestInstanceToPaginator
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     */
    public function handle($event): void
    {
        PaginationState::resolveUsing($event->sandbox);
    }
}

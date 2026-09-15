<?php

namespace Ugarit\Octane\Events;

use Heritage\Foundation\Application;

class TickReceived
{
    public function __construct(
        public Application $app,
        public Application $sandbox
    ) {
    }
}

<?php

namespace Ugarit\Octane\Events;

use Heritage\Foundation\Application;

class TaskReceived
{
    public function __construct(
        public Application $app,
        public Application $sandbox,
        public $data
    ) {
    }
}

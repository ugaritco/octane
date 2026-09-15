<?php

namespace Ugarit\Octane\Events;

use Heritage\Foundation\Application;

class WorkerStarting
{
    public function __construct(public Application $app)
    {
    }
}

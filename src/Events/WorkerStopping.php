<?php

namespace Ugarit\Octane\Events;

use Heritage\Foundation\Application;

class WorkerStopping
{
    public function __construct(public Application $app)
    {
    }
}

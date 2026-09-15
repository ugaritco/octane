<?php

namespace Ugarit\Octane\Events;

use Heritage\Foundation\Application;
use Throwable;

class WorkerErrorOccurred
{
    public function __construct(public Throwable $exception, public Application $sandbox)
    {
    }
}

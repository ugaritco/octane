<?php

namespace Ugarit\Octane\Events;

use Heritage\Foundation\Application;
use Ugarit\Octane\Contracts\OperationTerminated;

class TickTerminated implements OperationTerminated
{
    use HasApplicationAndSandbox;

    public function __construct(
        public Application $app,
        public Application $sandbox
    ) {
    }
}

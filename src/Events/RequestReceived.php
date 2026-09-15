<?php

namespace Ugarit\Octane\Events;

use Heritage\Foundation\Application;
use Heritage\Http\Request;

class RequestReceived
{
    public function __construct(
        public Application $app,
        public Application $sandbox,
        public Request $request
    ) {
    }
}

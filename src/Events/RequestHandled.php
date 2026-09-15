<?php

namespace Ugarit\Octane\Events;

use Heritage\Foundation\Application;
use Heritage\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestHandled
{
    public function __construct(
        public Application $sandbox,
        public Request $request,
        public Response $response
    ) {
    }
}

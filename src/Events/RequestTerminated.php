<?php

namespace Ugarit\Octane\Events;

use Heritage\Foundation\Application;
use Heritage\Http\Request;
use Ugarit\Octane\Contracts\OperationTerminated;
use Symfony\Component\HttpFoundation\Response;

class RequestTerminated implements OperationTerminated
{
    use HasApplicationAndSandbox;

    public function __construct(
        public Application $app,
        public Application $sandbox,
        public Request $request,
        public Response $response
    ) {
    }
}

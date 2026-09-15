<?php

namespace Ugarit\Octane\Contracts;

use Heritage\Foundation\Application;
use Heritage\Http\Request;
use Ugarit\Octane\OctaneResponse;
use Ugarit\Octane\RequestContext;
use Throwable;

interface Client
{
    /**
     * Marshal the given request context into an Heritage request.
     */
    public function marshalRequest(RequestContext $context): array;

    /**
     * Send the response to the server.
     */
    public function respond(RequestContext $context, OctaneResponse $response): void;

    /**
     * Send an error message to the server.
     */
    public function error(Throwable $e, Application $app, Request $request, RequestContext $context): void;
}

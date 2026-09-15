<?php

namespace Ugarit\Octane\FrankenPhp;

use Heritage\Foundation\Application;
use Heritage\Http\Request;
use Ugarit\Octane\Contracts\Client;
use Ugarit\Octane\Octane;
use Ugarit\Octane\OctaneResponse;
use Ugarit\Octane\RequestContext;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class FrankenPhpClient implements Client
{
    /**
     * Marshal the given request context into an Heritage request.
     */
    public function marshalRequest(RequestContext $context): array
    {
        return [
            Request::capture(),
            $context,
        ];
    }

    /**
     * Send the response to the server.
     */
    public function respond(RequestContext $context, OctaneResponse $octaneResponse): void
    {
        $octaneResponse->response->send();
    }

    /**
     * Send an error message to the server.
     */
    public function error(Throwable $e, Application $app, Request $request, RequestContext $context): void
    {
        $response = new Response(
            Octane::formatExceptionForClient($e, $app->make('config')->get('app.debug')),
            500,
            [
                'Status' => '500 Internal Server Error',
                'Content-Type' => 'text/plain',
            ],
        );

        $response->send();
    }
}

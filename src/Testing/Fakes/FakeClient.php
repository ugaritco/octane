<?php

namespace Ugarit\Octane\Testing\Fakes;

use Heritage\Foundation\Application;
use Heritage\Http\Request;
use Ugarit\Octane\Contracts\Client;
use Ugarit\Octane\OctaneResponse;
use Ugarit\Octane\RequestContext;
use Throwable;

class FakeClient implements Client
{
    public $index = 0;

    public $responses = [];

    public $errors = [];

    public function __construct(public array $requests)
    {
    }

    public function marshalRequest(RequestContext $context): array
    {
        return [$context->request, $context];
    }

    public function respond(RequestContext $context, OctaneResponse $octaneResponse): void
    {
        $this->responses[] = $octaneResponse->response;
    }

    public function error(Throwable $e, Application $app, Request $request, RequestContext $context): void
    {
        $message = $app->make('config')->get('app.debug') ? (string) $e : 'Internal server error.';

        $this->errors[] = $message;
    }
}

<?php

namespace Ugarit\Octane\Tests;

use Heritage\Http\Request;
use Ugarit\Octane\FrankenPhp\FrankenPhpClient;
use Ugarit\Octane\OctaneResponse;
use Ugarit\Octane\RequestContext;
use Symfony\Component\HttpFoundation\Response;

class FrankenPhpClientTest extends TestCase
{
    public function test_marshal_request()
    {
        $requestContext = new RequestContext();
        $marshaledRequest = (new FrankenPhpClient())->marshalRequest($requestContext);
        $this->assertInstanceOf(Request::class, $marshaledRequest[0]);
        $this->assertSame($requestContext, $marshaledRequest[1]);
    }

    public function test_response()
    {
        $response = \Mockery::mock(Response::class);
        $response->shouldReceive('send');

        (new FrankenPhpClient())->respond(new RequestContext(), new OctaneResponse($response));

        $this->assertTrue(true);
    }
}

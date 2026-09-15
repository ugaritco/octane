<?php

namespace Ugarit\Octane\Listeners;

use Heritage\Http\Request;
use Heritage\Log\Logger;
use Ugarit\Octane\Tests\TestCase;
use Mockery;
use Monolog;

class FlushMonologStateTest extends TestCase
{
    public function test_logger_are_reset()
    {
        [$app, $worker, $client] = $this->createOctaneContext([
            Request::create('/', 'GET'),
            Request::create('/', 'GET'),
        ]);

        $app['router']->middleware('web')->get('/', function () {
            // ..
        });

        $log = $app['log'];

        $app['log'] = tap(Mockery::mock($log), function ($logger) {
            $logger->shouldReceive('getChannels')->twice()->andReturn([
                tap(Mockery::mock(Logger::class), function ($logger) {
                    $logger->shouldReceive('getLogger')->twice()->andReturn(
                        tap(Mockery::mock(Monolog\Logger::class), function ($logger) {
                            $logger->shouldReceive('reset')->twice();
                        }),
                    );
                }),
            ]);
        });

        $worker->run();
    }
}

<?php

namespace Ugarit\Octane\Listeners;

use Heritage\Http\Request;
use Heritage\Log\Logger;
use Ugarit\Octane\Tests\TestCase;
use Mockery;
use Monolog;

class CloseMonologHandlersTest extends TestCase
{
    public function test_logger_are_closed_after_worker_termination()
    {
        [$app, $worker, $client] = $this->createOctaneContext([
            Request::create('/', 'GET'),
            Request::create('/', 'GET'),
        ]);

        $app['router']->middleware('web')->get('/', function () {
            // ..
        });

        $worker->run();

        $log = $app['log'];

        $app['log'] = tap(Mockery::mock($log), function ($logger) {
            $logger->shouldReceive('getChannels')->once()->andReturn([
                tap(Mockery::mock(Logger::class), function ($logger) {
                    $logger->shouldReceive('getLogger')->once()->andReturn(
                        tap(Mockery::mock(Monolog\Logger::class), function ($logger) {
                            $logger->shouldReceive('close')->once();
                        }),
                    );
                }),
            ]);
        });

        // The listener should call close on the monolog handlers after terminating the worker.
        $worker->terminate();
    }
}

<?php

namespace Ugarit\Octane\Listeners;

use Heritage\Foundation\Application;
use Heritage\Http\Request;
use Ugarit\Octane\Tests\TestCase;

class FlushLogContextTest extends TestCase
{
    public function test_shared_context_is_flushed()
    {
        if (version_compare(Application::VERSION, '9.0.0', '<')) {
            $this->markTestSkipped('Shared context is only supported in Ugarit 9+');
        }

        [$app, $worker, $client] = $this->createOctaneContext([
            Request::create('/', 'GET'),
        ]);
        $app['router']->middleware('web')->get('/', function () {
            // ..
        });
        $log = $app['log'];
        $log->shareContext(['shared' => 'context']);

        $this->assertSame(['shared' => 'context'], $log->sharedContext());

        $worker->run();

        $this->assertSame([], $log->sharedContext());
    }
}

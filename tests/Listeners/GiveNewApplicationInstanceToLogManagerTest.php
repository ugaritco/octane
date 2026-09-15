<?php

namespace Ugarit\Octane\Tests\Listeners;

use Heritage\Http\Request;
use Heritage\Support\Facades\Context;
use Heritage\Support\Facades\Log;
use Ugarit\Octane\Tests\TestCase;

class GiveNewApplicationInstanceToLogManagerTest extends TestCase
{
    public function test_context_is_appened_to_logs()
    {
        if (! class_exists(Context::class)) {
            $this->markTestSkipped('Context is not available in this version of Ugarit.');
        }
        [$app, $worker, $client] = $this->createOctaneContext([
            Request::create('/', 'GET'),
        ]);
        $path = $app['config']->get('logging.channels.single.path');
        @unlink($path);

        $this->assertFileDoesNotExist($path);

        $app['router']->middleware('web')->get('/', function () {
            Context::add('foo', 'bar');
            Log::info('Hello world');
        });

        $worker->run();

        $this->assertStringContainsString('{"foo":"bar"}', file_get_contents($path));
    }
}

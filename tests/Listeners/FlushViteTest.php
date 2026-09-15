<?php

namespace Ugarit\Octane\Listeners;

use Heritage\Foundation\Vite;
use Heritage\Http\Request;
use Ugarit\Octane\Tests\TestCase;

class FlushViteTest extends TestCase
{
    public function test_it_flushes_vite()
    {
        [$app, $worker] = $this->createOctaneContext([
            Request::create('/', 'GET'),
            Request::create('/', 'GET'),
            Request::create('/', 'GET'),
        ]);
        $app['router']->get('/', fn () => 'ok');

        $app->instance(Vite::class, $vite = new class extends Vite
        {
            public int $flushCalled = 0;

            public function flush()
            {
                $this->flushCalled++;
            }
        });
        $worker->run();

        $this->assertSame(3, $vite->flushCalled);
    }

    public function test_it_handles_instances_of_vite_without_flush_method()
    {
        [$app, $worker] = $this->createOctaneContext([
            Request::create('/', 'GET'),
            Request::create('/', 'GET'),
            Request::create('/', 'GET'),
        ]);
        $app['router']->get('/', fn () => 'ok');

        $app->instance(Vite::class, new class {
            //
        });
        $worker->run();

        $this->assertTrue(true);
    }
}

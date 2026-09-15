<?php

namespace Ugarit\Octane\Listeners;

use Heritage\Http\Request;
use Heritage\Support\Once;
use Ugarit\Octane\Tests\TestCase;

class FlushOnceTest extends TestCase
{
    public function test_once_is_flushed()
    {
        if (! class_exists(Once::class)) {
            $this->markTestSkipped('Once is only supported in Ugarit 11+');
        }

        [$app, $worker] = $this->createOctaneContext([
            Request::create('/', 'GET'),
            Request::create('/', 'GET'),
            Request::create('/', 'GET'),
        ]);

        $results = [];

        $app['router']->middleware('web')->get('/', function () use (&$results) {
            $results[] = my_rand();
        });

        $worker->run();

        $this->assertTrue($results[0] !== $results[1]);
        $this->assertTrue($results[0] !== $results[2]);
        $this->assertTrue($results[1] !== $results[2]);
    }
}

function my_rand()
{
    return once(fn () => rand(1, PHP_INT_MAX));
}

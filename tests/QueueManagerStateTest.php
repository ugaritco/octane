<?php

namespace Ugarit\Octane\Tests;

use Heritage\Foundation\Application;
use Heritage\Http\Request;

class QueueManagerStateTest extends TestCase
{
    public function test_queue_manager_has_fresh_application_instance()
    {
        [$app, $worker, $client] = $this->createOctaneContext([
            Request::create('/first', 'GET'),
            Request::create('/first', 'GET'),
        ]);

        $app['queue']->connection('sync');

        $app['router']->get('/first', function (Application $app) {
            return spl_object_hash($app['queue']->connection('sync')->getContainer());
        });

        $worker->run();

        $this->assertNotEquals($client->responses[0]->original, $client->responses[1]->original);
    }
}

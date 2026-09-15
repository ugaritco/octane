<?php

namespace Ugarit\Octane\Tests;

use Heritage\Broadcasting\BroadcastManager;
use Heritage\Foundation\Application;
use Heritage\Http\Request;

class BroadcastManagerStateTest extends TestCase
{
    public function test_broadcast_manager_has_fresh_application_instance()
    {
        [$app, $worker, $client] = $this->createOctaneContext([
            Request::create('/first?name=Taylor', 'GET'),
            Request::create('/first?name=Abigail', 'GET'),
        ]);

        $app[BroadcastManager::class];

        $app['router']->get('/first', function (Application $app) {
            return [
                'name' => $app[BroadcastManager::class]->getApplication()['request']->query('name'),
                'hash' => spl_object_hash($app[BroadcastManager::class]->getApplication()),
            ];
        });

        $worker->run();

        $this->assertEquals('Taylor', $client->responses[0]->original['name']);
        $this->assertEquals('Abigail', $client->responses[1]->original['name']);

        $this->assertNotEquals($client->responses[0]->original['hash'], $client->responses[1]->original['hash']);
    }
}

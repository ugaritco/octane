<?php

namespace Ugarit\Octane\Tests;

use Heritage\Foundation\Application;
use Heritage\Http\Request;

class MailManagerStateTest extends TestCase
{
    public function test_mail_manager_has_fresh_application_instance()
    {
        [$app, $worker, $client] = $this->createOctaneContext([
            Request::create('/first', 'GET'),
            Request::create('/first', 'GET'),
        ]);

        $app['mail.manager'];

        $app['router']->get('/first', function (Application $app) {
            return spl_object_hash($app['mail.manager']->getApplication());
        });

        $worker->run();

        $this->assertNotEquals($client->responses[0]->original, $client->responses[1]->original);
    }
}

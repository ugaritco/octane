<?php

namespace Ugarit\Octane\Testing\Fakes;

use Ugarit\Octane\RequestContext;
use Ugarit\Octane\Worker;

class FakeWorker extends Worker
{
    public function run()
    {
        foreach ($this->client->requests as $request) {
            [$request, $context] = $this->client->marshalRequest(
                new RequestContext(['request' => $request])
            );

            $this->handle($request, $context);
        }
    }

    public function runTasks()
    {
        return collect($this->client->requests)->map(fn ($data) => $this->handleTask($data))->all();
    }

    public function runTicks()
    {
        return collect($this->client->requests)->map(fn () => $this->handleTick())->all();
    }
}

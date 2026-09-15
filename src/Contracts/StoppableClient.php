<?php

namespace Ugarit\Octane\Contracts;

interface StoppableClient extends Client
{
    /**
     * Stop the underlying server / worker.
     */
    public function stop(): void;
}

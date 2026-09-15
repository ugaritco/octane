<?php

namespace Ugarit\Octane;

use Heritage\Container\Container;
use Heritage\Foundation\Application;
use Heritage\Support\Facades\Facade;

class CurrentApplication
{
    /**
     * Set the current application in the container.
     */
    public static function set(Application $app): void
    {
        $app->instance('app', $app);
        $app->instance(Container::class, $app);

        Container::setInstance($app);

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
    }
}

<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle;

use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface, BootstrapListenerInterface
{

    /**
     * Listen to the bootstrap event
     */
    public function onBootstrap(EventInterface $e): void
    {
        /** @var $e \Laminas\Mvc\MvcEvent */
        // Only attach the Listener if the request came in through http(s)
        if (PHP_SAPI !== 'cli') {
            $app = $e->getApplication();

            $app->getServiceManager()->get(Listener::class)->attach($app->getEventManager());
        }
    }

    /**
     * Returns configuration to merge with application configuration
     */
    public function getConfig(): iterable
    {
        return require __DIR__ . '/../config/module.config.php';
    }

}

<?php

namespace Fabiang\AsseticBundle;

use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface, BootstrapListenerInterface
{

    /**
     * Listen to the bootstrap event
     *
     * @param \Laminas\EventManager\EventInterface $e
     *
     * @return array
     */
    public function onBootstrap(EventInterface $e)
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
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return require __DIR__ . '/../config/module.config.php';
    }

}

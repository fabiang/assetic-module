<?php

namespace Fabiang\AsseticBundle;

use Laminas\Mvc\Application;
use Laminas\View\Renderer;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager'       => [
        'aliases'   => [
            'AsseticConfiguration'  => Configuration::class,
            'AsseticService'        => Service::class,
            'Assetic\FilterManager' => FilterManager::class,
        ],
        'factories' => [
            'AsseticBundle\Service'       => ServiceFactory::class,
            'Assetic\AssetWriter'         => WriterFactory::class,
            'AsseticBundle\FilterManager' => FilterManagerFactory::class,
            'Assetic\AssetManager'        => InvokableFactory::class,
            'AsseticBundle\Listener'      => InvokableFactory::class,
            'AsseticBundle\Cli'           => Cli\ApplicationFactory::class,
            'AsseticBundle\Configuration' => Factory\ConfigurationFactory::class,
        ],
    ],
    'assetic_configuration' => [
        'rendererToStrategy' => [
            Renderer\PhpRenderer::class  => View\ViewHelperStrategy::class,
            Renderer\FeedRenderer::class => View\NoneStrategy::class,
            Renderer\JsonRenderer::class => View\NoneStrategy::class,
        ],
        'acceptableErrors'   => [
            Application::ERROR_CONTROLLER_NOT_FOUND,
            Application::ERROR_CONTROLLER_INVALID,
            Application::ERROR_ROUTER_NO_MATCH
        ],
    ],
];

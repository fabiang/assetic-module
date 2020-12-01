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
            'Assetic\AssetManager'      => InvokableFactory::class,
            'Assetic\AssetWriter'       => Factory\WriterFactory::class,
            Service::class              => Factory\ServiceFactory::class,
            FilterManager::class        => Factory\FilterManagerFactory::class,
            Listener::class             => InvokableFactory::class,
            'Fabiang\AsseticBundle\Cli' => Cli\ApplicationFactory::class,
            Configuration::class        => Factory\ConfigurationFactory::class,
            AsseticMiddleware::class    => Factory\AsseticMiddlewareFactory::class,
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

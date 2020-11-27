<?php

use Zend\Mvc\Application;
use Laminas\View\Renderer;
use AsseticBundle\View;

return [
    'service_manager'       => [
        'aliases'   => [
            'AsseticConfiguration'  => 'AsseticBundle\Configuration',
            'AsseticService'        => 'AsseticBundle\Service',
            'Assetic\FilterManager' => 'AsseticBundle\FilterManager',
        ],
        'factories' => [
            'AsseticBundle\Service'       => 'AsseticBundle\ServiceFactory',
            'Assetic\AssetWriter'         => 'AsseticBundle\WriterFactory',
            'AsseticBundle\FilterManager' => 'AsseticBundle\FilterManagerFactory',
            'Assetic\AssetManager'        => 'Zend\ServiceManager\Factory\InvokableFactory',
            'AsseticBundle\Listener'      => 'Zend\ServiceManager\Factory\InvokableFactory',
            'AsseticBundle\Cli'           => 'AsseticBundle\Cli\ApplicationFactory',
            'AsseticBundle\Configuration' => 'AsseticBundle\Factory\ConfigurationFactory',
        ],
    ],
    'assetic_configuration' => [
        'rendererToStrategy' => [
            'Zend\View\Renderer\PhpRenderer'  => 'AsseticBundle\View\ViewHelperStrategy',
            'Zend\View\Renderer\FeedRenderer' => 'AsseticBundle\View\NoneStrategy',
            'Zend\View\Renderer\JsonRenderer' => 'AsseticBundle\View\NoneStrategy',
            Renderer\PhpRenderer::class       => View\ViewHelperStrategy::class,
            Renderer\FeedRenderer::class      => View\NoneStrategy::class,
            Renderer\JsonRenderer::class      => View\NoneStrategy::class,
        ],
        'acceptableErrors'   => [
            Application::ERROR_CONTROLLER_NOT_FOUND,
            Application::ERROR_CONTROLLER_INVALID,
            Application::ERROR_ROUTER_NO_MATCH
        ],
    ],
];

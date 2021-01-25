<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Fabiang\AsseticBundle\AsseticMiddleware;
use Fabiang\AsseticBundle\Service as AsseticService;
use Laminas\View\Renderer\PhpRenderer;

final class AsseticMiddlewareFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object
    {
        /** @var AsseticService $asseticService */
        $asseticService = $container->get(AsseticService::class);

        // Create or retrieve the renderer from the container
        $viewRenderer = ($container->has(PhpRenderer::class)) ? $container->get(PhpRenderer::class) : new PhpRenderer();

        return new AsseticMiddleware($asseticService, $viewRenderer);
    }

}

<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\Factory;

use Fabiang\AsseticBundle\AsseticMiddleware;
use Fabiang\AsseticBundle\Service as AsseticService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Renderer\PhpRenderer;
use Psr\Container\ContainerInterface;

final class AsseticMiddlewareFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): object
    {
        /** @var AsseticService $asseticService */
        $asseticService = $container->get(AsseticService::class);

        // Create or retrieve the renderer from the container
        $viewRenderer = $container->has(PhpRenderer::class) ? $container->get(PhpRenderer::class) : new PhpRenderer();

        return new AsseticMiddleware($asseticService, $viewRenderer);
    }
}

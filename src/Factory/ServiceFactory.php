<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\Factory;

use Assetic;
use Fabiang\AsseticBundle\Service;
use Laminas\Http\Request as HTTPRequest;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Mezzio\Application as MezzioApp;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

use function class_exists;
use function method_exists;

class ServiceFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Service
    {
        $asseticConfig = $container->get('AsseticConfiguration');
        if ($asseticConfig->detectBaseUrl()) {
            if (class_exists(MezzioApp::class)) { // is expressive app
                $urlHelper = $container->get(UrlHelper::class);
                $asseticConfig->setBaseUrl($urlHelper->getBasePath());
            } else {
                /** @var HTTPRequest $request */
                $request = $container->get('Request');
                if (method_exists($request, 'getBaseUrl')) {
                    $asseticConfig->setBaseUrl($request->getBaseUrl());
                }
            }
        }

        $asseticService = new Service($asseticConfig);
        $asseticService->setAssetManager($container->get(Assetic\AssetManager::class));
        $asseticService->setAssetWriter($container->get(Assetic\AssetWriter::class));
        $asseticService->setFilterManager($container->get(Assetic\FilterManager::class));

        // Cache buster is not mandatory
        if ($container->has('AsseticCacheBuster')) {
            $asseticService->setCacheBusterStrategy($container->get('AsseticCacheBuster'));
        }

        return $asseticService;
    }
}

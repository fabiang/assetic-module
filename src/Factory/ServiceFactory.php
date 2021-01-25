<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Assetic;
use Mezzio\Helper\UrlHelper;
use Mezzio\Application as MezzioApp;
use Fabiang\AsseticBundle\Service;

class ServiceFactory implements FactoryInterface
{

    /**
     * @param string $requestedName
     * @return Service
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $asseticConfig = $container->get('AsseticConfiguration');
        if ($asseticConfig->detectBaseUrl()) {
            /** @var \Laminas\Http\PhpEnvironment\Request $request */
            if (class_exists(MezzioApp::class)) { // is expressive app
                $urlHelper = $container->get(UrlHelper::class);
                $asseticConfig->setBaseUrl($urlHelper->getBasePath());
            } else {
                /** @var \Laminas\Http\Request $request */
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

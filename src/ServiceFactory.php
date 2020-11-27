<?php

namespace Fabiang\AsseticBundle;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ServiceFactory implements FactoryInterface
{

    /**
     * @param string $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $asseticConfig = $container->get('AsseticConfiguration');
        if ($asseticConfig->detectBaseUrl()) {
            /** @var $request \Laminas\Http\PhpEnvironment\Request */
            $request = $locator->get('Request');
            if (method_exists($request, 'getBaseUrl')) {
                $asseticConfig->setBaseUrl($request->getBaseUrl());
            }
        }

        $asseticService = new Service($asseticConfig);
        $asseticService->setAssetManager($locator->get('Assetic\AssetManager'));
        $asseticService->setAssetWriter($locator->get('Assetic\AssetWriter'));
        $asseticService->setFilterManager($locator->get('Assetic\FilterManager'));

        // Cache buster is not mandatory
        if ($locator->has('AsseticCacheBuster')) {
            $asseticService->setCacheBusterStrategy($locator->get('AsseticCacheBuster'));
        }

        return $asseticService;
    }

}

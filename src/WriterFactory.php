<?php

namespace Fabiang\AsseticBundle;

use Assetic\AssetWriter;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class WriterFactory implements FactoryInterface
{

    /**
     * @param string $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $asseticConfig = $container->get('AsseticConfiguration');
        return new AssetWriter($asseticConfig->getWebPath());
    }

}

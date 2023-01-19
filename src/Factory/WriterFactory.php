<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\Factory;

use Assetic\AssetWriter;
use interop\container\containerinterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class WriterFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @return AssetWriter
     */
    public function __invoke(containerinterface $container, $requestedName, ?array $options = null)
    {
        $asseticConfig = $container->get('AsseticConfiguration');
        return new AssetWriter($asseticConfig->getWebPath());
    }
}

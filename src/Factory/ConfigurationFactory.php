<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\Factory;

use Fabiang\AsseticBundle\Configuration;
use interop\container\containerinterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ConfigurationFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @return Configuration
     */
    public function __invoke(containerinterface $container, $requestedName, ?array $options = null)
    {
        $configuration = $container->get('config');
        return new Configuration($configuration['assetic_configuration']);
    }
}

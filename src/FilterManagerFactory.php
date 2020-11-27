<?php

namespace Fabiang\AsseticBundle;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FilterManagerFactory implements FactoryInterface
{

    /**
     * @param string $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FilterManager($container);
    }

}

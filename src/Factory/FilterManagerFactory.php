<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\Factory;

use Fabiang\AsseticBundle\FilterManager;
use interop\container\containerinterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FilterManagerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @return FilterManager
     */
    public function __invoke(containerinterface $container, $requestedName, ?array $options = null)
    {
        return new FilterManager($container);
    }
}

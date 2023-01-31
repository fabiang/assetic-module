<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\Factory;

use Fabiang\AsseticBundle\FilterManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class FilterManagerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @return FilterManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new FilterManager($container);
    }
}

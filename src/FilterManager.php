<?php

namespace Fabiang\AsseticBundle;

use Assetic\Contracts\Filter\FilterInterface;
use Assetic\FilterManager as AsseticFilterManager;
use Interop\Container\ContainerInterface;
use Fabiang\AsseticBundle\Exception\InvalidArgumentException;

class FilterManager extends AsseticFilterManager
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $alias
     *
     * @return bool
     */
    public function has($alias)
    {
        return parent::has($alias) ? true : $this->container->has($alias);
    }

    /**
     * @param $alias
     *
     * @throws \InvalidArgumentException    When cant retrieve filter from service manager.
     *
     * @return mixed
     */
    public function get($alias)
    {
        if (parent::has($alias)) {
            return parent::get($alias);
        }

        $service = $this->container;
        if (!$service->has($alias)) {
            throw new InvalidArgumentException(sprintf('There is no "%s" filter in ZF2 service manager.', $alias));
        }

        $filter = $service->get($alias);
        if (!($filter instanceof FilterInterface)) {
            $givenType = is_object($filter) ? get_class($filter) : gettype($filter);
            $message   = 'Retrieved filter "%s" is not instanceof "Assetic\Filter\FilterInterface", but type was given %s';
            $message   = sprintf($message, $alias, $givenType);
            throw new InvalidArgumentException($message);
        }

        $this->set($alias, $filter);

        return $filter;
    }

}

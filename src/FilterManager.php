<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle;

use Assetic\Contracts\Filter\FilterInterface;
use Assetic\FilterManager as AsseticFilterManager;
use Fabiang\AsseticBundle\Exception\InvalidArgumentException;
use interop\container\containerinterface;

use function gettype;
use function is_object;
use function sprintf;

class FilterManager extends AsseticFilterManager
{
    protected containerinterface $container;

    public function __construct(containerinterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param mixed $alias
     */
    public function has($alias): bool
    {
        return parent::has($alias) ? true : $this->container->has($alias);
    }

    /**
     * @param mixed $alias
     * @return mixed
     * @throws InvalidArgumentException    When cant retrieve filter from service manager.
     */
    public function get($alias)
    {
        if (parent::has($alias)) {
            return parent::get($alias);
        }

        $service = $this->container;
        if (! $service->has($alias)) {
            throw new InvalidArgumentException(
                sprintf(
                    'There is no "%s" filter in Laminas service manager.',
                    $alias
                )
            );
        }

        $filter = $service->get($alias);
        if (! $filter instanceof FilterInterface) {
            $givenType = is_object($filter) ? FilterInterface::class : gettype($filter);
            $message   = 'Retrieved filter "%s" is not instanceof '
                . '"Assetic\Filter\FilterInterface", but type was given %s';
            $message   = sprintf($message, $alias, $givenType);
            throw new InvalidArgumentException($message);
        }

        $this->set($alias, $filter);

        return $filter;
    }
}

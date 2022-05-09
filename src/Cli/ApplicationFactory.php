<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\Cli;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Symfony\Component\Console\Application;

class ApplicationFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @return Application
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $cliApplication = new Application('AsseticBundle', '3.x');

        $cliApplication->add(new BuildCommand($container->get('AsseticService')));
        $cliApplication->add(new SetupCommand($container->get('AsseticService')));

        return $cliApplication;
    }
}

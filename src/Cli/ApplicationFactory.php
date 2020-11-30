<?php

namespace Fabiang\AsseticBundle\Cli;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ApplicationFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array $options, optional
     *
     * @return Application
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $cliApplication = new Application('AsseticBundle', '3.x');

        $cliApplication->add(new BuildCommand($container->get('AsseticService')));
        $cliApplication->add(new SetupCommand($container->get('AsseticService')));

        return $cliApplication;
    }

}

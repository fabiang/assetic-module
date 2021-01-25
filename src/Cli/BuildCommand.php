<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\Cli;

use Fabiang\AsseticBundle\Service;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{

    /**
     * The assetic service

     */
    private Service $assetic;

    /**
     * Constructor.
     */
    public function __construct(Service $assetic)
    {
        parent::__construct('build');
        $this->assetic = $assetic;
        $this->setDescription('Build all assets.');
    }

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->assetic->getConfiguration();
        $config->setBuildOnRequest(true);
        $this->assetic->build();
        $this->assetic->getAssetWriter()->writeManagerAssets($this->assetic->getAssetManager());
        return 0;
    }

}

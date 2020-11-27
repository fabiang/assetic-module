<?php

namespace AsseticBundle\CacheBuster;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Factory\Worker\WorkerInterface;
use Assetic\Factory\AssetFactory;

class Null implements WorkerInterface
{

    public function process(AssetInterface $asset, AssetFactory $factory)
    {

    }

}

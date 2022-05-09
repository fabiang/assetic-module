<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\CacheBuster;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Factory\Worker\WorkerInterface;
use Assetic\Factory\AssetFactory;

class NullStrategy implements WorkerInterface
{
    public function process(AssetInterface $asset, AssetFactory $factory): ?AssetInterface
    {
        return null;
    }
}

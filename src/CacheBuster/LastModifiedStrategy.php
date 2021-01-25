<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\CacheBuster;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Factory\Worker\WorkerInterface;
use Assetic\Factory\AssetFactory;

class LastModifiedStrategy implements WorkerInterface
{

    public function process(AssetInterface $asset, AssetFactory $factory): ?AssetInterface
    {
        $path = $asset->getTargetPath();
        if (null === $path) {
            return null;
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION);

        $lastModified = $factory->getLastModified($asset);
        if (null !== $lastModified) {
            $path = substr_replace(
                $path,
                "$lastModified.$ext",
                -1 * strlen($ext)
            );
            $asset->setTargetPath($path);
        }
        return null;
    }

}

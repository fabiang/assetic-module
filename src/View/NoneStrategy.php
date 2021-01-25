<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\View;

use Assetic\Contracts\Asset\AssetInterface;

class NoneStrategy extends AbstractStrategy
{

    public function setupAsset(AssetInterface $asset): void
    {

    }

}

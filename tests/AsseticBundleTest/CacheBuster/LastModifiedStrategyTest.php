<?php

namespace AsseticBundleTest\CacheBuster;

use Fabiang\AsseticBundle\CacheBuster\LastModifiedStrategy;
use Assetic\Asset\FileAsset;
use Assetic\Factory\AssetFactory;
use PHPUnit\Framework\TestCase;

class LastModifiedStrategyTest extends TestCase
{

    public function setUp(): void
    {
        $this->cacheBuster = new LastModifiedStrategy();
    }

    public function testAssetLastModifiedTimestampIsPrependBeforeFileExtension()
    {
        $asset = new FileAsset(TEST_ASSETS_DIR . '/css/global.css');
        $asset->setTargetPath(TEST_PUBLIC_DIR . '/css/global.css');

        $factory = new AssetFactory('');

        $this->cacheBuster->process($asset, $factory);

        $this->assertSame(
            TEST_PUBLIC_DIR . '/css/global.' . $asset->getLastModified() . '.css',
            $asset->getTargetPath()
        );
    }

}

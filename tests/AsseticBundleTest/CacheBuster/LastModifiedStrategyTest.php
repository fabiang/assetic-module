<?php

declare(strict_types=1);

namespace AsseticBundleTest\CacheBuster;

use Assetic\Asset\FileAsset;
use Assetic\Factory\AssetFactory;
use Fabiang\AsseticBundle\CacheBuster\LastModifiedStrategy;
use PHPUnit\Framework\TestCase;

final class LastModifiedStrategyTest extends TestCase
{
    private LastModifiedStrategy $cacheBuster;

    public function setUp(): void
    {
        $this->cacheBuster = new LastModifiedStrategy();
    }

    public function testAssetLastModifiedTimestampIsPrependBeforeFileExtension(): void
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

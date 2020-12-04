<?php

declare(strict_types=1);

namespace AsseticBundleTest;

use PHPUnit\Framework\TestCase;
use Fabiang\AsseticBundle\ConfigProvider;

/**
 * @coversDefaultClass Fabiang\AsseticBundle\ConfigProvider
 */
class ConfigProviderTest extends TestCase
{

    private ConfigProvider $config;

    protected function setUp(): void
    {
        $this->config = new ConfigProvider();
    }

    /**
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        $this->assertTrue(is_callable($this->config));

        $result = $this->config->__invoke();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertArrayNotHasKey('service_manager', $result);
    }

}

<?php

declare(strict_types=1);

namespace AsseticBundleTest;

use Fabiang\AsseticBundle\AsseticMiddleware;
use Fabiang\AsseticBundle\Factory\AsseticMiddlewareFactory;
use Fabiang\AsseticBundle\Service;
use interop\container\containerinterface;
use Laminas\View\Renderer\PhpRenderer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use function is_callable;

/**
 * @coversDefaultClass Fabiang\AsseticBundle\Factory\AsseticMiddlewareFactory
 */
final class AsseticMiddlewareFactoryTest extends TestCase
{
    use ProphecyTrait;

    private AsseticMiddlewareFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new AsseticMiddlewareFactory();
    }

    /**
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        $this->assertTrue(is_callable($this->factory));

        $asseticService = $this->prophesize(Service::class);
        $phpRenderer    = $this->prophesize(PhpRenderer::class);

        $container = $this->prophesize(containerinterface::class);
        $container->get(Service::class)
            ->shouldBeCalled()
            ->willReturn($asseticService->reveal());

        $container->has(PhpRenderer::class)
            ->willReturn(true);

        $container->get(PhpRenderer::class)
            ->shouldBeCalled()
            ->willReturn($phpRenderer->reveal());

        $this->assertInstanceOf(
            AsseticMiddleware::class,
            $this->factory->__invoke($container->reveal(), AsseticMiddleware::class, [])
        );
    }
}

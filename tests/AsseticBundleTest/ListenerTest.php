<?php

declare(strict_types=1);

namespace AsseticBundleTest;

use Fabiang\AsseticBundle\Configuration;
use Fabiang\AsseticBundle\Listener;
use Fabiang\AsseticBundle\Service as AsseticService;
use interop\container\containerinterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\Application as MvcApplication;
use Laminas\Mvc\ApplicationInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteMatch;
use Laminas\Stdlib\ResponseInterface;
use Laminas\View\Renderer\RendererInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @coversDefaultClass \Fabiang\AsseticBundle\Listener
 */
final class ListenerTest extends TestCase
{
    use ProphecyTrait;

    private Listener $listener;

    protected function setUp(): void
    {
        $this->listener = new Listener();
    }

    /**
     * @covers ::attach
     */
    public function testAttach(): void
    {
        $events = $this->prophesize(EventManagerInterface::class);
        $events->attach(
            MvcEvent::EVENT_DISPATCH,
            [$this->listener, 'renderAssets'],
            2
        )
            ->shouldBeCalled();
        $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this->listener, 'renderAssets'],
            2
        )
            ->shouldBeCalled();

        $this->listener->attach($events->reveal(), 2);
    }

    public function testRenderAssets(): void
    {
        $conf    = new Configuration();
        $service = $this->prophesize(AsseticService::class);

        $routeMatch = new RouteMatch([]);
        $routeMatch->setMatchedRouteName('myroutename');
        $routeMatch->setParam('controller', 'mycontroller');
        $routeMatch->setParam('action', 'myaction');

        $service->setRouteName('myroutename')
            ->shouldBeCalled();

        $service->setControllerName('mycontroller')
            ->shouldBeCalled();

        $service->setActionName('myaction')
            ->shouldBeCalled();

        $service->build()
            ->shouldBeCalled();
        $service->setupRenderer(Argument::type(RendererInterface::class))
            ->shouldBeCalled()
            ->willReturn(true);

        $renderer = $this->prophesize(RendererInterface::class);

        $app = $this->prophesize(ApplicationInterface::class);
        $sm  = $this->prophesize(containerinterface::class);
        $sm->get('AsseticConfiguration')->willReturn($conf);
        $sm->get('AsseticService')->willReturn($service->reveal());
        $sm->get('ViewRenderer')->willReturn($renderer->reveal());

        $app->getServiceManager()->willReturn($sm->reveal());

        $e = new MvcEvent();
        $e->setApplication($app->reveal());
        $e->setName(MvcEvent::EVENT_DISPATCH);
        $e->setRouteMatch($routeMatch);

        $this->listener->renderAssets($e);
        $this->assertInstanceOf(ResponseInterface::class, $e->getResponse());
    }

    public function testRenderAssetsDispatchError(): void
    {
        $conf = new Configuration();
        $conf->setAcceptableErrors([
            MvcApplication::ERROR_CONTROLLER_INVALID,
        ]);

        $app = $this->prophesize(ApplicationInterface::class);
        $sm  = $this->prophesize(containerinterface::class);
        $sm->get('AsseticConfiguration')->willReturn($conf);
        $sm->get('AsseticService')->shouldNotBeCalled();
        $app->getServiceManager()->willReturn($sm->reveal());

        $e = new MvcEvent();
        $e->setApplication($app->reveal());
        $e->setName(MvcEvent::EVENT_DISPATCH_ERROR);
        $e->setError(MvcApplication::ERROR_CONTROLLER_NOT_FOUND);

        $this->listener->renderAssets($e);
    }
}

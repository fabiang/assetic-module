<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle;

use Laminas\View\Renderer\RendererInterface as Renderer;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AsseticMiddleware implements MiddlewareInterface
{
    private Service $asseticService;
    private Renderer $viewRenderer;

    public function __construct(Service $asseticService, Renderer $viewRenderer)
    {
        $this->asseticService = $asseticService;
        $this->viewRenderer   = $viewRenderer;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->renderAssets($request);
        return $handler->handle($request);
    }

    public function renderAssets(ServerRequestInterface $request): void
    {
        $asseticService = $this->asseticService;
        $routeResult    = $request->getAttribute(RouteResult::class);

        if ($routeResult) {
            $actionName = $request->getAttribute('action', 'index');
            $moduleName = $request->getAttribute('controller', $request->getAttribute('module', $actionName));

            $asseticService->setRouteName($routeResult->getMatchedRouteName());
            $asseticService->setControllerName($actionName);
            $asseticService->setActionName($moduleName);
        }

        // Create all objects
        $asseticService->build();

        // Init assets for modules
        $asseticService->setupRenderer($this->viewRenderer);
    }
}

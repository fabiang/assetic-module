<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\ResponseInterface;

use function in_array;

class Listener extends AbstractListenerAggregate
{
    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 32): void
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH,
            [$this, 'renderAssets'],
            $priority
        );
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'renderAssets'],
            $priority
        );
    }

    public function renderAssets(MvcEvent $e): void
    {
        $sm = $e->getApplication()->getServiceManager();

        /** @var Configuration $config */
        $config = $sm->get('AsseticConfiguration');
        if ($e->getName() === MvcEvent::EVENT_DISPATCH_ERROR) {
            $error = $e->getError();
            if ($error && ! in_array($error, $config->getAcceptableErrors())) {
                // break if not an acceptable error
                return;
            }
        }

        /** @var ResponseInterface|null $response */
        $response = $e->getResponse();
        if (! $response) {
            $response = new Response();
            $e->setResponse($response);
        }

        /** @var Service $asseticService */
        $asseticService = $sm->get('AsseticService');

        // setup service if a matched route exist
        $router = $e->getRouteMatch();
        if ($router) {
            $asseticService->setRouteName($router->getMatchedRouteName());
            $asseticService->setControllerName($router->getParam('controller'));
            $asseticService->setActionName($router->getParam('action'));
        }

        // Create all objects
        $asseticService->build();

        // Init assets for modules
        $asseticService->setupRenderer($sm->get('ViewRenderer'));
    }
}

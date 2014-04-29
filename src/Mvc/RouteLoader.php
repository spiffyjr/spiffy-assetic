<?php

namespace Spiffy\Assetic;

use Assetic\Asset\AssetInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * The majority of this code was a port from AsseticBundle.
 * @see https://github.com/symfony/AsseticBundle/blob/master/Routing/AsseticLoader.php
 */
class RouteLoader extends AbstractListenerAggregate
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $events->attach(MvcEvent::EVENT_BOOTSTRAP, [$this, 'onBootstrap'], -9999);
    }

    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $services = $app->getServiceManager();

        /** @var \Spiffy\Assetic\ModuleOptions $options */
        $options = $services->get('Spiffy\Assetic\ModuleOptions');

        if (!$options->getAutoload()) {
            return;
        }

        /** @var \Spiffy\Assetic\AsseticService $asseticService */
        $asseticService = $i->nvoke('Spiffy\Assetic\AsseticService');
        $asseticService->load();

        $am = $asseticService->getAssetManager();

        /** @var \Spiffy\Route\Router $router */
        $router = $i->nvoke('router');

        foreach ($am->getNames() as $name) {
            if (!$am->hasFormula($name)) {
                continue;
            }

            $asset = $am->get($name);
            $formula = $am->getFormula($name);

            $this->loadRouteForAsset($router, $asset, $name);

            $debug = isset($formula[2]['debug']) ? $formula[2]['debug'] : $am->isDebug();
            $combine = isset($formula[2]['combine']) ? $formula[2]['combine'] : !$debug;

            if (!$combine) {
                $i = 0;
                foreach ($asset as $leaf) {
                    $this->loadRouteForAsset($router, $leaf, $name, $i++);
                }
            }
        }
    }

    /**
     * @param Router $router
     * @param AssetInterface $asset
     * @param $name
     * @param null $pos
     */
    protected function loadRouteForAsset(Router $router, AssetInterface $asset, $name, $pos = null)
    {
        $defaults = [
            'controller' => 'spiffy.assetic-package.asset',
            'name' => $name,
            'pos' => $pos,
        ];

        if ($format = pathinfo($asset->getTargetPath(), PATHINFO_EXTENSION)) {
            $defaults['format'] = $format;
        }

        $route = 'spiffy.assetic-package.' . $name;

        if (null !== $pos) {
            $route.= '_' . $pos;
        }

        $router->add($route, $asset->getTargetPath(), ['defaults' => $defaults]);
    }
}

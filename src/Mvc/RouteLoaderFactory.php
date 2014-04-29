<?php

namespace Spiffy\Assetic;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RouteLoaderFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @return RouteLoader
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return new RouteLoader(
            $services->get('Spiffy\Assetic\AsseticService'),
            $services->get('router')
        );
    }
}

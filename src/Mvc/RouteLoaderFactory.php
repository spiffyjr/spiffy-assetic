<?php

namespace SpiffyAssetic;

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
            $services->get('SpiffyAssetic\AsseticService'),
            $services->get('router')
        );
    }
}

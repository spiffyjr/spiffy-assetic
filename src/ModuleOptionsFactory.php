<?php

namespace SpiffyAssetic;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ModuleOptionsFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @return ModuleOptions
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return new ModuleOptions($services->get('Config')['spiffy-assetic']);
    }
}

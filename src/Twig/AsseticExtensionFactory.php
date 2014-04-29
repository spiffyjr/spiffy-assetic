<?php

namespace SpiffyAssetic\Twig;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AsseticExtensionFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @return AsseticExtension
     */
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var \SpiffyAssetic\AsseticService $service */
        $service = $services->get('SpiffyAssetic\AsseticService');
        $factory = $service->getAssetFactory();

        return new AsseticExtension($factory);
    }
}

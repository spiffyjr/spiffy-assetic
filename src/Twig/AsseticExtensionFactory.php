<?php

namespace Spiffy\Assetic\Twig;

use Assetic\Extension\Twig\AsseticExtension;
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
        /** @var \Spiffy\Assetic\AsseticService $service */
        $service = $services->get('Spiffy\Assetic\AsseticService');
        $factory = $service->getAssetFactory();

        return new AsseticExtension($factory);
    }
}

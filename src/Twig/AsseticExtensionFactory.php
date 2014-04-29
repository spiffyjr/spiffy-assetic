<?php

namespace Spiffy\Assetic\Twig;

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

        /** @var \Spiffy\Assetic\ModuleOptions $options */
        $options = $services->get('Spiffy\Assetic\ModuleOptions');

        return new AsseticExtension($factory, $options->getParsers());
    }
}

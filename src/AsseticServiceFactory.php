<?php

namespace Spiffy\Assetic;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AsseticServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @return AsseticService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var \Spiffy\Assetic\ModuleOptions $options */
        $options = $services->get('Spiffy\Assetic\ModuleOptions');
        $factory = $services->get('Assetic\Factory\AssetFactory');

        $service = new AsseticService($factory, $services->get('Assetic\AssetWriter'));
        $this->injectPlugins($services, $service, $options->getPlugins());

        return $service;
    }

    /**
     * @param ServiceLocatorInterface $services
     * @param AsseticService $service
     * @param array $plugins
     */
    protected function injectPlugins(ServiceLocatorInterface $services, AsseticService $service, array $plugins)
    {
        foreach ($plugins as $plugin) {
            if (empty($plugin)) {
                continue;
            }

            if (is_string($plugin)) {
                $plugin = $services->has($plugin) ? $services->get($plugin) : new $plugin();
            }

            $service->getEventManager()->attach($plugin);
        }
    }
}

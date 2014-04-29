<?php

namespace SpiffyAssetic\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AssetLoaderPluginFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @return AssetLoaderPlugin
     */
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var \SpiffyAssetic\ModuleOptions $options */
        $options = $services->get('SpiffyAssetic\ModuleOptions');

        return new AssetLoaderPlugin($options->getAssets());
    }
}

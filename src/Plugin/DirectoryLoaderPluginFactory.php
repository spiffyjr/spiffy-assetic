<?php

namespace SpiffyAssetic\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DirectoryLoaderPluginFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @return DirectoryLoaderPlugin
     */
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var \SpiffyAssetic\ModuleOptions $options */
        $options = $services->get('SpiffyAssetic\ModuleOptions');

        return new DirectoryLoaderPlugin(
            $services->get('Assetic\Factory\AssetFactory'),
            $options->getDirectories()
        );
    }
}

<?php

namespace Spiffy\Assetic\Plugin;

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
        /** @var \Spiffy\Assetic\ModuleOptions $options */
        $options = $services->get('Spiffy\Assetic\ModuleOptions');

        return new DirectoryLoaderPlugin(
            $services->get('Assetic\Factory\AssetFactory'),
            $options->getDirectories()
        );
    }
}

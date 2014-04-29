<?php

namespace Spiffy\Assetic\Assetic;

use Assetic\AssetWriter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AssetWriterFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @return AssetWriter
     */
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var \Spiffy\Assetic\ModuleOptions $options */
        $options = $services->get('Spiffy\Assetic\ModuleOptions');

        return new AssetWriter($options->getOutputDir());
    }
}

<?php

namespace SpiffyAssetic\Assetic;

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
        /** @var \SpiffyAssetic\ModuleOptions $options */
        $options = $services->get('SpiffyAssetic\ModuleOptions');

        return new AssetWriter($options->getOutputDir());
    }
}

<?php

namespace Spiffy\Assetic\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TwigLoaderPluginFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @return TwigLoaderPlugin
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return new TwigLoaderPlugin($services->get('Twig_Environment'));
    }
}

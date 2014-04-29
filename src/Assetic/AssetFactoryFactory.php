<?php

namespace Spiffy\Assetic\Assetic;

use Assetic\Factory\LazyAssetManager;
use Assetic\FilterManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AssetFactoryFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @return AssetFactory
     */
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var \Zend\ModuleManager\ModuleManager $pm */
        $moduleManager = $services->get('ModuleManager');

        /** @var \Spiffy\Assetic\ModuleOptions $options */
        $options = $services->get('Spiffy\Assetic\ModuleOptions');

        $factory = new AssetFactory($moduleManager, $options->getRootDir(), $options->getDebug());
        $assetManager = new LazyAssetManager($factory);
        $filterManager = new FilterManager();

        $factory->setAssetManager($assetManager);
        $factory->setFilterManager($filterManager);

        return $factory;
    }
}

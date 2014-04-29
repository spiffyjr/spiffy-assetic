<?php

namespace Spiffy\Assetic\Plugin;

use Assetic\Cache\ConfigCache;
use Assetic\Factory\LazyAssetManager;
use Assetic\Factory\Loader\CachedFormulaLoader;
use Spiffy\Assetic\Assetic\AssetFactory;
use Spiffy\Assetic\AsseticService;
use Spiffy\Assetic\Loader\DirectoryFormulaLoader;
use Spiffy\Assetic\Loader\RecursiveDirectoryResource;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

class DirectoryLoaderPlugin extends AbstractListenerAggregate
{
    /**
     * @var AssetFactory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $directories;

    /**
     * @param \Spiffy\Assetic\Assetic\AssetFactory $factory
     * @param array $directories
     */
    public function __construct(AssetFactory $factory, array $directories)
    {
        $this->directories = $directories;
        $this->factory = $factory;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $events->attach(AsseticService::EVENT_LOAD, [$this, 'onLoad']);
    }

    /**
     * @param EventInterface $e
     */
    public function onLoad(EventInterface $e)
    {
        /** @var \Spiffy\Assetic\AsseticService $service */
        $service = $e->getTarget();
        $am = $service->getAssetManager();

        if (!$am instanceof LazyAssetManager) {
            return;
        }

        foreach ($this->directories as $outputName => $spec) {
            $formulaName = 'directories_ ' . $outputName;
            $formulaLoader = new CachedFormulaLoader(
                new DirectoryFormulaLoader($service->getAssetFactory(), $outputName),
                new ConfigCache('data/cache/assetic'),
                $am->isDebug()
            );

            $am->setLoader($formulaName, $formulaLoader);

            foreach ($spec as $directory) {
                $am->addResource(new RecursiveDirectoryResource(
                    $this->factory->convertModuleInput($directory[0]),
                    isset($directory[1]) ? $directory[1] : '*'
                ), $formulaName);
            }
        }
    }
}

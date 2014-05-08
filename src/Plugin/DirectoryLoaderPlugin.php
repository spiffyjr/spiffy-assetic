<?php

namespace Spiffy\Assetic\Plugin;

use Assetic\Cache\ConfigCache;
use Assetic\Factory\LazyAssetManager;
use Assetic\Factory\Loader\CachedFormulaLoader;
use Spiffy\Assetic\Assetic\DirectoryFormulaLoader;
use Spiffy\Assetic\Assetic\RecursiveDirectoryResource;
use Spiffy\Assetic\AsseticService;
use Spiffy\Event\Event;
use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;

class DirectoryLoaderPlugin implements Plugin
{
    /**
     * @var AsseticService
     */
    protected $asseticService;

    /**
     * @var array
     */
    protected $directories;

    /**
     * @param array $directories
     */
    public function __construct(array $directories)
    {
        $this->directories = $directories;
    }

    /**
     * @param Manager $events
     */
    public function plug(Manager $events)
    {
        $events->on(AsseticService::EVENT_LOAD, [$this, 'onLoad']);
    }

    /**
     * @param Event $e
     */
    public function onLoad(Event $e)
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
                if (empty($directory)) {
                    continue;
                }
                $am->addResource(new RecursiveDirectoryResource(
                    $service->resolveAlias($directory[0]),
                    isset($directory[1]) ? $directory[1] : '*'
                ), $formulaName);
            }
        }
    }
}

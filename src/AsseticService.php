<?php

namespace SpiffyAssetic;

use Assetic\AssetWriter;
use Zend\EventManager\EventManagerAwareTrait;

class AsseticService
{
    use EventManagerAwareTrait;

    const EVENT_LOAD = 'spiffy-assetic.load';

    /**
     * @var Assetic\AssetFactory
     */
    protected $factory;

    /**
     * @var AssetWriter
     */
    protected $writer;

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @param Assetic\AssetFactory $factory
     * @param AssetWriter $writer
     */
    public function __construct(Assetic\AssetFactory $factory, AssetWriter $writer)
    {
        $this->factory = $factory;
        $this->writer = $writer;
    }

    /**
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded;
    }

    /**
     * Trigger load event to prepare manager.
     */
    public function load()
    {
        if ($this->loaded) {
            return;
        }
        $this->loaded = true;
        $this->getEventManager()->trigger(self::EVENT_LOAD, $this);
    }

    /**
     * Clear asset manager
     */
    public function clear()
    {
        $this->factory->getAssetManager()->clear();
        $this->loaded = false;
    }

    /**
     * Write assets.
     */
    public function write()
    {
        $this->load();
        $writer = $this->getWriter();
        $writer->writeManagerAssets($this->getAssetManager());
    }

    /**
     * @return AssetWriter
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * @return Assetic\AssetFactory
     */
    public function getAssetFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Assetic\Factory\LazyAssetManager
     */
    public function getAssetManager()
    {
        return $this->factory->getAssetManager();
    }

    /**
     * @return \Assetic\FilterManager
     */
    public function getFilterManager()
    {
        return $this->factory->getFilterManager();
    }
}

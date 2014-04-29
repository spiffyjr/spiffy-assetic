<?php

namespace SpiffyAssetic\Plugin;

use SpiffyAssetic\AsseticService;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

class AssetLoaderPlugin extends AbstractListenerAggregate
{
    /**
     * @var array
     */
    protected $assets = [];

    /**
     * @param array $assets
     */
    public function __construct(array $assets)
    {
        $this->assets = $assets;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $events->attach(AsseticService::EVENT_LOAD, [$this, 'onLoad'], 1000);
    }

    /**
     * @param EventInterface $e
     */
    public function onLoad(EventInterface $e)
    {
        if (empty($this->assets)) {
            return;
        }

        /** @var \SpiffyAssetic\AsseticService $service */
        $service = $e->getTarget();
        $manager = $service->getAssetManager();
        $factory = $service->getAssetFactory();

        foreach ($this->assets as $name => $asset) {
            if (!is_array($asset)) {
                continue;
            }

            $inputs = isset($asset[0]) ? $asset[0] : [];
            $filters = isset($asset[1]) ? $asset[1] : [];
            $options = isset($asset[2]) ? $asset[2] : [];

            $manager->set($name, $factory->createAsset($inputs, $filters, $options));
        }
    }
}

<?php

namespace Spiffy\Assetic\Plugin;

use Spiffy\Assetic\AsseticService;
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

        /** @var \Spiffy\Assetic\AsseticService $service */
        $service = $e->getTarget();
        $manager = $service->getAssetManager();
        $factory = $service->getAssetFactory();

        foreach ($this->assets as $name => $asset) {
            if (!is_array($asset)) {
                continue;
            }

            $inputs = isset($asset['inputs']) ? $asset['inputs'] : [];
            $filters = isset($asset['filters']) ? $asset['filters'] : [];
            $options = isset($asset['options']) ? $asset['options'] : [];

            $manager->set($name, $factory->createAsset($inputs, $filters, $options));
        }
    }
}

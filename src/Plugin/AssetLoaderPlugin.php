<?php

namespace Spiffy\Assetic\Plugin;

use Spiffy\Assetic\AsseticService;
use Spiffy\Event\Event;
use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;

class AssetLoaderPlugin implements Plugin
{
    /**
     * @var array
     */
    protected $assets;

    /**
     * @param array $assets
     */
    public function __construct(array $assets)
    {
        $this->assets = $assets;
    }

    /**
     * @param Manager $events
     */
    public function plug(Manager $events)
    {
        $events->on(AsseticService::EVENT_LOAD, [$this, 'onLoad']);
    }


    /**
     * @param Event $event
     */
    public function onLoad(Event $event)
    {
        /** @var \Spiffy\Assetic\AsseticService $asseticService */
        $asseticService = $event->getTarget();
        $manager = $asseticService->getAssetManager();
        $factory = $asseticService->getAssetFactory();

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

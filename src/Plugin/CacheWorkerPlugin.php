<?php

namespace Spiffy\Assetic\Plugin;

use Assetic\Factory\Worker\CacheBustingWorker;
use Spiffy\Assetic\AsseticService;
use Spiffy\Event\Event;
use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;

class CacheWorkerPlugin implements Plugin
{
    /**
     * @param Manager $events
     * @return void
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
        $factory = $service->getAssetFactory();

        $factory->addWorker(new CacheBustingWorker($service->getAssetManager()));
    }
}

<?php

namespace Spiffy\Assetic\Plugin;

use Assetic\Factory\Worker\CacheBustingWorker;
use Spiffy\Assetic\AsseticService;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

class CacheWorkerPlugin extends AbstractListenerAggregate
{
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
        /** @var \Spiffy\Assetic\AsseticService $service */
        $service = $e->getTarget();
        $factory = $service->getAssetFactory();

        $factory->addWorker(new CacheBustingWorker($service->getAssetManager()));
    }
}

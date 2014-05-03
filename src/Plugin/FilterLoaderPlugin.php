<?php

namespace Spiffy\Assetic\Plugin;

use Spiffy\Assetic\AsseticService;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

class FilterLoaderPlugin extends AbstractListenerAggregate
{
    /**
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $events->attach(AsseticService::EVENT_LOAD, [$this, 'onLoad'], 1500);
    }

    /**
     * @param EventInterface $e
     */
    public function onLoad(EventInterface $e)
    {
        if (empty($this->filters)) {
            return;
        }

        /** @var \Spiffy\Assetic\AsseticService $service */
        $service = $e->getTarget();
        $manager = $service->getFilterManager();

        foreach ($this->filters as $name => $filter) {
            $manager->set($name, $filter);
        }
    }
}

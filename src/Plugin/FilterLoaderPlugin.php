<?php

namespace Spiffy\Assetic\Plugin;

use Assetic\Filter\FilterInterface;
use Spiffy\Assetic\AsseticService;
use Spiffy\Event\Event;
use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;

class FilterLoaderPlugin implements Plugin
{
    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param Manager $events
     */
    public function plug(Manager $events)
    {
        $events->on(AsseticService::EVENT_LOAD, [$this, 'onLoad'], 10000);
    }

    /**
     * @param Event $event
     */
    public function onLoad(Event $event)
    {
        if ($this->loaded) {
            return;
        }

        $this->loaded = true;

        /** @var \Assetic\FilterManager $filterManager */
        $filterManager = $event->getTarget()->getFilterManager();

        foreach ($this->filters as $name => $filter) {
            if (empty($filter)) {
                continue;
            }
            $filterManager->set($name, $this->loadFilter($filter));
        }
    }

    /**
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded;
    }

    /**
     * @param mixed $filter
     * @return FilterInterface
     */
    protected function loadFilter($filter)
    {
        if ($filter instanceof FilterInterface) {
            return $filter;
        }
        return new $filter();
    }
}

<?php

namespace SpiffyAssetic\Mvc;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * Triggers during onDispatch() to ensure that the assetic service has loaded all assets. By this time all
 * plugins should be loaded and the asset manager should be ready.
 */
class RenderListener extends AbstractListenerAggregate
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $events->attach(MvcEvent::EVENT_RENDER, [$this, 'onRender'], 9999);
    }

    /**
     * @param MvcEvent $e
     */
    public function onRender(MvcEvent $e)
    {
        $app = $e->getApplication();
        $services = $app->getServiceManager();
        $services->get('SpiffyAssetic\AsseticService')->load();
    }
}

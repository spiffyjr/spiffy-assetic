<?php

namespace Spiffy\Assetic\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FilterLoaderPluginFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @return FilterLoaderPlugin
     */
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var \Spiffy\Assetic\ModuleOptions $options */
        $options = $services->get('Spiffy\Assetic\ModuleOptions');
        $filters = $options->getFilters();

        foreach ($filters as &$filter) {
            if (is_string($filter)) {
                $filter = $services->has($filter) ? $services->get($filter) : new $filter();
            }
        }

        return new FilterLoaderPlugin($filters);
    }
}

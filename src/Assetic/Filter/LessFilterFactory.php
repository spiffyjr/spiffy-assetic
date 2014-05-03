<?php

namespace Spiffy\Assetic\Assetic\Filter;

use Assetic\Filter\LessFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LessFilterFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @return LessFilter
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $config = $services->get('Config');
        $options = isset($config['spiffy-assetic']['filter_options']['less']) ?
            $config['spiffy-assetic']['filter_options']['less'] : [];

        $filter = new LessFilter($options['node_bin'], $options['node_paths']);
        $filter->setLoadPaths($options['load_paths']);

        return $filter;
    }
}

<?php

namespace Spiffy\Assetic;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var bool
     */
    protected $autoload = false;

    /**
     * @var string
     */
    protected $rootDir = './';

    /**
     * @var string
     */
    protected $outputDir = 'public';

    /**
     * @var array
     */
    protected $assets = [];

    /**
     * @var array
     */
    protected $directories = [];

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var array
     */
    protected $filterOptions = [];

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var array
     */
    protected $consolePlugins = [];

    /**
     * @var array
     */
    protected $plugins = [];

    /**
     * @param array $assets
     */
    public function setAssets($assets)
    {
        $this->assets = $assets;
    }

    /**
     * @return array
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * @param boolean $autoload
     */
    public function setAutoload($autoload)
    {
        $this->autoload = $autoload;
    }

    /**
     * @return boolean
     */
    public function getAutoload()
    {
        return $this->autoload;
    }

    /**
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @return boolean
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * @param array $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param string $outputDir
     */
    public function setOutputDir($outputDir)
    {
        $this->outputDir = $outputDir;
    }

    /**
     * @return string
     */
    public function getOutputDir()
    {
        return $this->outputDir;
    }

    /**
     * @param array $plugins
     */
    public function setPlugins($plugins)
    {
        $this->plugins = $plugins;
    }

    /**
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @param string $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * @param array $variables
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param array $filterOptions
     */
    public function setFilterOptions($filterOptions)
    {
        $this->filterOptions = $filterOptions;
    }

    /**
     * @return array
     */
    public function getFilterOptions()
    {
        return $this->filterOptions;
    }

    /**
     * @param array $directories
     */
    public function setDirectories($directories)
    {
        $this->directories = $directories;
    }

    /**
     * @return array
     */
    public function getDirectories()
    {
        return $this->directories;
    }

    /**
     * @param array $consolePlugins
     */
    public function setConsolePlugins($consolePlugins)
    {
        $this->consolePlugins = $consolePlugins;
    }

    /**
     * @return array
     */
    public function getConsolePlugins()
    {
        return $this->consolePlugins;
    }
}

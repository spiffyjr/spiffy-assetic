<?php

namespace Spiffy\Assetic\Assetic;

use Assetic\Factory\AssetFactory as BaseAssetFactory;
use Zend\ModuleManager\ModuleManager;

class AssetFactory extends BaseAssetFactory
{
    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var array
     */
    protected $pathCache = [];

    /**
     * @param ModuleManager $moduleManager
     * @param string $root
     * @param bool $debug
     */
    public function __construct(ModuleManager $moduleManager, $root, $debug = false)
    {
        $this->moduleManager = $moduleManager;
        parent::__construct($root, $debug);
    }


    /**
     * @param string $input
     * @return string
     */
    public function convertModuleInput($input)
    {
        if ('@' != $input[0] || false == strpos($input, '/')) {
            return $input;
        }

        $moduleName = substr($input, 1);
        if (false !== $pos = strpos($moduleName, '/')) {
            $moduleName = substr($moduleName, 0, $pos);
        }

        return str_replace('@' . $moduleName, $this->getModulePath($moduleName), $input);
    }

    /**
     * @param string $moduleName
     * @return string
     */
    public function getModulePath($moduleName)
    {
        if (isset($this->pathCache[$moduleName])) {
            return $this->pathCache[$moduleName];
        }

        $module = $this->moduleManager->getModule($moduleName);
        $refl = new \ReflectionClass($module);
        $path = dirname($refl->getFileName());

        if (!file_exists($path . '/src')) {
            $path = realpath($path . '/../../');
        }

        $this->pathCache[$moduleName] = $path;
        return $path;
    }

    /**
     * {@inheritDoc}
     */
    protected function parseInput($input, array $options = [])
    {
        $input = $this->convertModuleInput($input);

        return parent::parseInput($input, $options);
    }
}

<?php

namespace Spiffy\Assetic;

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Factory\LazyAssetManager;
use Assetic\FilterManager;
use Assetic\Util\VarUtils;
use Spiffy\Assetic\Assetic\AssetFactory;
use Spiffy\Event\Event;
use Spiffy\Event\EventsAwareTrait;

class AsseticService
{
    use EventsAwareTrait;

    const EVENT_LOAD = 'spiffy-assetic:load';
    const EVENT_DUMP_ASSET = 'spiffy-assetic:dump-asset';
    const EVENT_DUMP_DIR = 'spiffy-assetic:dump-dir';
    const EVENT_DUMP_TARGET = 'spiffy-assetic:dump-target';
    const EVENT_RESOLVE_ALIAS = 'spiffy-assetic:resolve-alias';
    const EVENT_WATCH_ERROR = 'spiffy-assetic:watch-error';

    /**
     * @var AssetFactory
     */
    protected $assetFactory;

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var string
     */
    protected $root;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @param string $root
     * @param bool $debug
     */
    public function __construct($root, $debug = false)
    {
        $this->root = $root;
        $this->debug = $debug;
    }

    /**
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded;
    }

    /**
     * Trigger load event to prepare manager.
     */
    public function load()
    {
        if ($this->loaded) {
            return;
        }
        $this->loaded = true;
        $this->events()->fire(self::EVENT_LOAD, $this);
    }

    /**
     * Clear asset manager
     */
    public function clear()
    {
        $this->getAssetFactory()->getAssetManager()->clear();
        $this->loaded = false;
    }

    /**
     * @param string $input
     * @param array $params
     * @return string
     */
    public function resolveAlias($input, array $params = [])
    {
        $params['assetic_service'] = $this;

        $event = new Event(self::EVENT_RESOLVE_ALIAS, $input, $params);
        $this->events()->fire($event);

        return $event->getTarget();
    }

    /**
     * @param string $outputDir
     * @param array $variables
     * @param bool $verbose
     */
    public function writeAssets($outputDir, array $variables = [], $verbose = false)
    {
        foreach ($this->getAssetManager()->getNames() as $name) {
            $this->writeAsset($name, $outputDir, $variables, $verbose);
        }
    }

    /**
     * @todo Figure out how to test this beast?
     * @codeCoverageIgnore
     * @param string $outputDir
     * @param bool $force
     * @param int $period
     * @param array $variables
     * @param bool $verbose
     */
    public function watchAssets($outputDir, $force, $period = 1, array $variables = [], $verbose = false)
    {
        $cache = sys_get_temp_dir() . '/assetic_watch_' . substr(sha1($outputDir), 0, 7);
        if ($force || !file_exists($cache)) {
            $previously = [];
        } else {
            $previously = unserialize(file_get_contents($cache));
            if (!is_array($previously)) {
                $previously = [];
            }
        }

        $error = '';
        while (true) {
            try {
                foreach ($this->getAssetManager()->getNames() as $name) {
                    if ($this->checkAsset($name, $variables, $previously)) {
                        $this->writeAsset($name, $outputDir, $variables, $verbose);
                    }
                }

                $this->clear();
                $this->load();

                file_put_contents($cache, serialize($previously));
                $error = '';
            } catch (\Exception $e) {
                if ($error != $e->getMessage()) {
                    $this->events()->fire(self::EVENT_WATCH_ERROR, $e);
                    $error = $e->getMessage();
                }
            }

            sleep($period);
        }
    }

    /**
     * @param string $name
     * @param array $variables
     * @param array $previously
     * @return bool
     */
    public function checkAsset($name, array $variables = [], array &$previously)
    {
        $am = $this->getAssetManager();

        $formula = $am->hasFormula($name) ? serialize($am->getFormula($name)) : null;
        $asset = $am->get($name);

        $combinations = VarUtils::getCombinations(
            $asset->getVars(),
            $variables
        );

        $mtime = 0;
        foreach ($combinations as $combination) {
            $asset->setValues($combination);
            $mtime = max($mtime, $am->getLastModified($asset));
        }

        if (isset($previously[$name])) {
            $changed = $previously[$name]['mtime'] != $mtime || $previously[$name]['formula'] != $formula;
        } else {
            $changed = true;
        }

        $previously[$name] = ['mtime' => $mtime, 'formula' => $formula];

        return $changed;
    }

    /**
     * @param string $name
     * @param string $outputDir
     * @param array $variables
     * @param bool $verbose
     */
    public function writeAsset($name, $outputDir, array $variables = [], $verbose = false)
    {
        $am = $this->getAssetManager();
        $asset = $am->get($name);
        $formula = $am->hasFormula($name) ? $am->getFormula($name) : [];

        // start by dumping the main asset
        $this->doDump($asset, $outputDir, $variables, $verbose);

        // dump each leaf if debug
        if (isset($formula[2]['debug']) ? $formula[2]['debug'] : $am->isDebug()) {
            foreach ($asset as $leaf) {
                $this->doDump($leaf, $outputDir, $variables, $verbose);
            }
        }
    }

    /**
     * @return AssetFactory
     */
    public function getAssetFactory()
    {
        if (!$this->assetFactory instanceof Assetic\AssetFactory) {
            $factory = $this->assetFactory = new Assetic\AssetFactory($this);
            $factory->setAssetManager(new LazyAssetManager($factory));
            $factory->setFilterManager(new FilterManager());
        }
        return $this->assetFactory;
    }

    /**
     * @return \Assetic\Factory\LazyAssetManager
     */
    public function getAssetManager()
    {
        return $this->getAssetFactory()->getAssetManager();
    }

    /**
     * @return \Assetic\FilterManager
     */
    public function getFilterManager()
    {
        return $this->getAssetFactory()->getFilterManager();
    }

    /**
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param AssetInterface $asset
     * @param string $outputDir
     * @param array $variables
     * @param bool $verbose
     * @throws \RuntimeException
     */
    private function doDump(AssetInterface $asset, $outputDir, array $variables = [], $verbose = false)
    {
        $combinations = VarUtils::getCombinations(
            $asset->getVars(),
            $variables
        );

        foreach ($combinations as $combination) {
            $asset->setValues($combination);

            // resolve the target path
            $target = rtrim($outputDir, '/') . '/' . $asset->getTargetPath();
            $target = str_replace('_controller/', '', $target);
            $target = VarUtils::resolve($target, $asset->getVars(), $asset->getValues());

            $dir = dirname($target);
            if (!is_dir($dir)) {
                $this->events()->fire(self::EVENT_DUMP_DIR, $dir);

                if (false === mkdir($dir, 0777, true)) {
                    throw new \RuntimeException(sprintf(
                        'Unable to create directory %s',
                        $dir
                    ));
                }
            }

            $this->events()->fire(self::EVENT_DUMP_TARGET, $target);

            if ($verbose) {
                if ($asset instanceof AssetCollectionInterface) {
                    foreach ($asset as $leaf) {
                        $this->events()->fire(self::EVENT_DUMP_ASSET, $leaf);
                    }
                } else {
                    $this->events()->fire(self::EVENT_DUMP_ASSET, $asset);
                }
            }

            if (false === file_put_contents($target, $asset->dump())) {
                throw new \RuntimeException(sprintf(
                    'Unable to write file %s',
                    $target
                ));
            }
        }
    }
}

<?php

namespace SpiffyAssetic\Controller;

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Util\VarUtils;
use SpiffyAssetic\AsseticService;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Console\ColorInterface;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController
{
    /**
     * @param AsseticService $asseticService
     */
    public function __construct(AsseticService $asseticService)
    {
        $this->asseticService = $asseticService;
    }

    public function dumpAction()
    {
        /** @var \Zend\Console\Adapter\AdapterInterface $console */
        $console = $this->getServiceLocator()->get('console');
        $am = $this->getAssetManager();

        // print the header
        $console->writeLine(sprintf('Dumping all assets.'));
        $console->write('Debug mode is ');
        $console->writeLine($am->isDebug() ? 'on' : 'off', ColorInterface::YELLOW);
        $console->writeLine('');

        foreach ($am->getNames() as $name) {
            $this->dumpAsset($name, $console);
        }
    }

    public function watchAction()
    {
        $services = $this->getServiceLocator();

        /** @var \Zend\Console\Request $request */
        $request = $this->getRequest();

        /** @var \Zend\Console\Adapter\AdapterInterface $console */
        $console = $this->getServiceLocator()->get('console');
        $am = $this->getAssetManager();

        /** @var \SpiffyAssetic\ModuleOptions $options */
        $options = $services->get('SpiffyAssetic\ModuleOptions');

        // print the header
        $console->writeLine(sprintf('Dumping all assets.'));
        $console->write('Debug mode is ');
        $console->writeLine($am->isDebug() ? 'on' : 'off', ColorInterface::YELLOW);
        $console->writeLine('');

        // establish a temporary status file
        $cache = sys_get_temp_dir() . '/assetic_watch_' . substr(sha1($options->getOutputDir()), 0, 7);
        if ($request->getParam('force') || !file_exists($cache)) {
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
                foreach ($am->getNames() as $name) {
                    if ($this->checkAsset($name, $previously)) {
                        $this->dumpAsset($name, $console);
                    }
                }

                // reset the asset manager
                $this->asseticService->clear();
                $this->asseticService->load();

                file_put_contents($cache, serialize($previously));
                $error = '';
            } catch (\Exception $e) {
                if ($error != $e->getMessage()) {
                    $console->writeLine('[error] ' . $e->getMessage(), ColorInterface::WHITE, ColorInterface::RED);
                    $error = $e->getMessage();
                }
            }

            sleep($request->getParam('period'));
        }
    }

    /**
     * @return \Assetic\Factory\LazyAssetManager
     */
    public function getAssetManager()
    {
        return $this->asseticService->getAssetManager();
    }

    /**
     * @param string $name
     * @param array $previously
     * @return bool
     */
    protected function checkAsset($name, array &$previously)
    {
        $am = $this->getAssetManager();

        $formula = $am->hasFormula($name) ? serialize($am->getFormula($name)) : null;
        $asset = $am->get($name);
        $services = $this->getServiceLocator();

        /** @var \SpiffyAssetic\ModuleOptions $options */
        $options = $services->get('SpiffyAssetic\ModuleOptions');

        $combinations = VarUtils::getCombinations(
            $asset->getVars(),
            $options->getVariables()
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

        $previously[$name] = array('mtime' => $mtime, 'formula' => $formula);

        return $changed;
    }

    /**
     * @param $name
     * @param AdapterInterface $stdout
     */
    protected function dumpAsset($name, AdapterInterface $stdout)
    {
        $am = $this->getAssetManager();

        $asset = $am->get($name);
        $formula = $am->hasFormula($name) ? $am->getFormula($name) : [];

        // start by dumping the main asset
        $this->doDump($asset, $stdout);

        // dump each leaf if debug
        if (isset($formula[2]['debug']) ? $formula[2]['debug'] : $am->isDebug()) {
            foreach ($asset as $leaf) {
                $this->doDump($leaf, $stdout);
            }
        }
    }

    /**
     * @param AssetInterface $asset
     * @param AdapterInterface $stdout
     * @throws \RuntimeException
     */
    protected function doDump(AssetInterface $asset, AdapterInterface $stdout)
    {
        /** @var \Zend\Console\Request $request */
        $request = $this->getRequest();
        $verbose = $request->getParam('verbose');
        $services = $this->getServiceLocator();

        /** @var \SpiffyAssetic\ModuleOptions $options */
        $options = $services->get('SpiffyAssetic\ModuleOptions');

        $combinations = VarUtils::getCombinations(
            $asset->getVars(),
            $options->getVariables()
        );

        foreach ($combinations as $combination) {
            $asset->setValues($combination);

            // resolve the target path
            $target = rtrim($options->getOutputDir(), '/') . '/' . $asset->getTargetPath();
            $target = str_replace('_controller/', '', $target);
            $target = VarUtils::resolve($target, $asset->getVars(), $asset->getValues());

            if (!is_dir($dir = dirname($target))) {
                $stdout->write(date('H:i:s'), ColorInterface::GREEN);
                $stdout->write('  ');
                $stdout->write('[dir+]', ColorInterface::YELLOW);
                $stdout->write(' ');
                $stdout->writeLine($dir);

                if (false === @mkdir($dir, 0777, true)) {
                    throw new \RuntimeException('Unable to create directory '.$dir);
                }
            }

            $stdout->write(date('H:i:s'), ColorInterface::GREEN);
            $stdout->write(' ');
            $stdout->write('[file+]', ColorInterface::YELLOW);
            $stdout->write(' ');
            $stdout->writeLine($target);


            if ($verbose) {
                if ($asset instanceof AssetCollectionInterface) {
                    foreach ($asset as $leaf) {
                        $root = $leaf->getSourceRoot();
                        $path = $leaf->getSourcePath();

                        $stdout->writeLine(sprintf('        %s/%s',$root ?: '[unknown root]', $path ?: '[unknown path]'), ColorInterface::GREEN);
                    }
                } else {
                    $root = $asset->getSourceRoot();
                    $path = $asset->getSourcePath();
                    $stdout->writeLine(sprintf('        %s/%s',$root ?: '[unknown root]', $path ?: '[unknown path]'), ColorInterface::GREEN);
                }
            }

            if (false === file_put_contents($target, $asset->dump())) {
                throw new \RuntimeException('Unable to write file '.$target);
            }
        }
    }
}

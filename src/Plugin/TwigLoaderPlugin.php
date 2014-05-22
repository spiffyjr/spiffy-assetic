<?php

namespace Spiffy\Assetic\Plugin;

use Assetic\Cache\ConfigCache;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Extension\Twig\TwigResource;
use Assetic\Factory\LazyAssetManager;
use Assetic\Factory\Loader\CachedFormulaLoader;
use Spiffy\Assetic\AsseticService;
use Spiffy\Event\Event;
use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Symfony\Component\Finder\Finder;

class TwigLoaderPlugin implements Plugin
{
    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param \Twig_Environment $twig
     * @param string $cacheDir
     */
    public function __construct(\Twig_Environment $twig, $cacheDir)
    {
        $this->twig = $twig;
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param Manager $events
     */
    public function plug(Manager $events)
    {
        $events->on(AsseticService::EVENT_LOAD, [$this, 'onLoad']);
    }

    /**
     * @param Event $e
     */
    public function onLoad(Event $e)
    {
        /** @var \Spiffy\Assetic\AsseticService $service */
        $service = $e->getTarget();
        $am = $service->getAssetManager();

        $twig = $this->twig;
        $this->load($am, $twig->getLoader());

        $formulaLoader = new CachedFormulaLoader(
            new TwigFormulaLoader($twig),
            new ConfigCache($this->cacheDir),
            $am->isDebug()
        );
        $am->setLoader('twig', $formulaLoader);
    }

    /**
     * @param LazyAssetManager $am
     * @param \Twig_LoaderInterface $loader
     */
    protected function load(LazyAssetManager $am, \Twig_LoaderInterface $loader)
    {
        if ($loader instanceof \Twig_Loader_Chain) {
            $this->loadChain($am, $loader);
            return;
        }

        if ($loader instanceof \Twig_Loader_Filesystem && count($loader->getPaths()) > 0) {
            $this->loadFilesystem($am, $loader);
            return;
        }

        if ($loader instanceof \Twig_Loader_String) {
            $this->addResource($am, new TwigResource($loader, 'string'));
            return;
        }

        if ($loader instanceof \Twig_Loader_Array) {
            $this->loadArray($am, $loader);
        }
    }

    /**
     * @param LazyAssetManager $am
     * @param TwigResource $resource
     */
    private function addResource(LazyAssetManager $am, TwigResource $resource)
    {
        $am->addResource($resource, 'twig');
    }

    /**
     * @param LazyAssetManager $am
     * @param \Twig_Loader_Array $loader
     */
    private function loadArray(LazyAssetManager $am, \Twig_Loader_Array $loader)
    {
        $refl = new \ReflectionClass($loader);
        $templates = $refl->getProperty('templates');
        $templates->setAccessible(true);

        foreach ($templates->getValue($loader) as $name => $template) {
            $this->addResource($am, new TwigResource($loader, $name));
        }
    }

    /**
     * @param LazyAssetManager $am
     * @param \Twig_Loader_Filesystem $loader
     */
    private function loadFilesystem(LazyAssetManager $am, \Twig_Loader_Filesystem $loader)
    {
        $finder = new Finder();
        $finder
            ->files()
            ->ignoreUnreadableDirs()
            ->name('*.twig')
            ->in($loader->getPaths());

        foreach ($finder as $template) {
            $this->addResource($am, new TwigResource($loader, $template->getRelativePathname()));
        }
    }

    /**
     * @param LazyAssetManager $am
     * @param \Twig_Loader_Chain $chain
     */
    private function loadChain(LazyAssetManager $am, \Twig_Loader_Chain $chain)
    {
        $refl = new \ReflectionClass($chain);
        $loaders = $refl->getProperty('loaders');
        $loaders->setAccessible(true);

        foreach ($loaders->getValue($chain) as $loader) {
            $this->load($am, $loader);
        }
    }
}

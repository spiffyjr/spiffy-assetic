<?php

namespace SpiffyAssetic\Plugin;

use Assetic\Cache\ConfigCache;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Extension\Twig\TwigResource;
use Assetic\Factory\LazyAssetManager;
use Assetic\Factory\Loader\CachedFormulaLoader;
use SpiffyAssetic\AsseticService;
use Symfony\Component\Finder\Finder;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use ZfcTwig\Twig\MapLoader;

class TwigLoaderPlugin extends AbstractListenerAggregate
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $events->attach(AsseticService::EVENT_LOAD, [$this, 'onLoad']);
    }

    /**
     * @param EventInterface $e
     */
    public function onLoad(EventInterface $e)
    {
        $twig = $this->twig;

        /** @var \Twig_Loader_Chain $loader */
        $chain = $twig->getLoader();
        $refl = new \ReflectionClass($chain);
        $loaders = $refl->getProperty('loaders');
        $loaders->setAccessible(true);
        $loaders = $loaders->getValue($chain);

        /** @var \SpiffyAssetic\AsseticService $service */
        $service = $e->getTarget();
        $am = $service->getAssetManager();

        if (!$am instanceof LazyAssetManager) {
            return;
        }

        $formulaLoader = new CachedFormulaLoader(
            new TwigFormulaLoader($twig),
            new ConfigCache('data/cache/assetic'),
            $am->isDebug()
        );
        $am->setLoader('twig', $formulaLoader);

        $finder = new Finder();
        $finder
            ->files()
            ->ignoreUnreadableDirs()
            ->name('*.twig');

        $count = 0;
        foreach ($loaders as $loader) {
            if ($loader instanceof \Twig_Loader_Filesystem) {
                $finder->in($loader->getPaths());
            } else if ($loader instanceof MapLoader) {
                $refl = new \ReflectionClass($loader);
                $map = $refl->getProperty('map');
                $map->setAccessible(true);
                $map = $map->getValue($loader);

                foreach ($map as $name => $path) {
                    $count++;
                    $am->addResource(new TwigResource($loader, $name), 'twig');
                }
            }
        }

        /** @var \Symfony\Component\Finder\SplFileInfo */
        foreach ($finder as $template) {
            $count++;
            $am->addResource(new TwigResource($loader, $template->getRelativePathname()), 'twig');
        }
    }
}

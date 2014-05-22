<?php

namespace Spiffy\Assetic\Plugin;

use Spiffy\Assetic\AsseticService;
use Spiffy\Event\Event;
use Spiffy\Event\EventManager;

/**
 * @coversDefaultClass \Spiffy\Assetic\Plugin\TwigLoaderPlugin
 */
class TwigLoaderPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct, ::plug
     */
    public function testPlug()
    {
        $manager = new EventManager();

        $p = new TwigLoaderPlugin(new \Twig_Environment(), sys_get_temp_dir());
        $p->plug($manager);

        $this->assertCount(1, $manager->getEvents(AsseticService::EVENT_LOAD));
    }

    /**
     * @covers ::__construct, ::onLoad
     */
    public function testOnLoadWithSimpleLoader()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_String());

        $service = new AsseticService(__DIR__ . '/../');
        $manager = $service->getAssetManager();

        $e = new Event();
        $e->setTarget($service);

        $p = new TwigLoaderPlugin($twig, sys_get_temp_dir());
        $p->onLoad($e);

        $resources = $manager->getResources();
        $this->assertCount(1, $resources);
        $this->assertInstanceOf('Assetic\Extension\Twig\TwigResource', $resources[0]);
    }

    /**
     * @covers ::__construct, ::onLoad
     */
    public function testOnLoadWithChainedLoader()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Chain([
            new \Twig_Loader_Filesystem(__DIR__ . '/../asset'),
            new \Twig_Loader_Array(['herpdaderp' => __DIR__ . '/../asset/template.twig'])
        ]));

        $service = new AsseticService(__DIR__ . '/../');
        $manager = $service->getAssetManager();

        $e = new Event();
        $e->setTarget($service);

        $p = new TwigLoaderPlugin($twig, sys_get_temp_dir());
        $p->onLoad($e);

        $resources = $manager->getResources();
        $this->assertCount(2, $resources);
        $this->assertInstanceOf('Assetic\Extension\Twig\TwigResource', $resources[0]);
        $this->assertInstanceOf('Assetic\Extension\Twig\TwigResource', $resources[1]);
    }
}

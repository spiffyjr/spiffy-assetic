<?php

namespace Spiffy\Assetic\Plugin;

use Spiffy\Assetic\AsseticService;
use Spiffy\Event\Event;
use Spiffy\Event\EventManager;

/**
 * @coversDefaultClass \Spiffy\Assetic\Plugin\FilterLoaderPlugin
 */
class FilterLoaderPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::plug
     */
    public function testPlug()
    {
        $manager = new EventManager();

        $p = new FilterLoaderPlugin([]);
        $p->plug($manager);

        $this->assertCount(1, $manager->getEvents(AsseticService::EVENT_LOAD));
    }

    /**
     * @covers ::__construct
     * @covers ::onLoad
     * @covers ::isLoaded
     */
    public function testOnLoadIsLoadedOnce()
    {
        $service = new AsseticService(__DIR__ . '/../');

        $e = new Event();
        $e->setTarget($service);

        $p = new FilterLoaderPlugin([]);
        $this->assertFalse($p->isLoaded());

        $p->onLoad($e);
        $this->assertTrue($p->isLoaded());

        $p->onLoad($e);
        $this->assertTrue($p->isLoaded());
    }

    /**
     * @covers ::__construct
     * @covers ::onLoad
     */
    public function testOnLoad()
    {
        $config = ['css' => 'Assetic\Filter\PhpCssEmbedFilter', 'empty' => ''];

        $service = new AsseticService(__DIR__ . '/../');
        $manager = $service->getFilterManager();

        $e = new Event();
        $e->setTarget($service);

        $p = new FilterLoaderPlugin($config);
        $p->onLoad($e);

        $this->assertTrue($manager->has('css'));
        $this->assertFalse($manager->has('doesnotexist'));
    }
}

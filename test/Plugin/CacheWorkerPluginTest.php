<?php

namespace Spiffy\Assetic\Plugin;

use Spiffy\Assetic\AsseticService;
use Spiffy\Event\Event;
use Spiffy\Event\EventManager;

/**
 * @coversDefaultClass \Spiffy\Assetic\Plugin\CacheWorkerPlugin
 */
class CacheWorkerPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::plug
     */
    public function testPlug()
    {
        $manager = new EventManager();

        $p = new CacheWorkerPlugin([]);
        $p->plug($manager);

        $this->assertCount(1, $manager->getEvents(AsseticService::EVENT_LOAD));
    }

    /**
     * @covers ::onLoad
     */
    public function testOnLoad()
    {
        $service = new AsseticService(__DIR__ . '/../');
        $factory = $service->getAssetFactory();

        $e = new Event();
        $e->setTarget($service);

        $p = new CacheWorkerPlugin();
        $p->onLoad($e);

        $refl = new \ReflectionClass('Assetic\Factory\AssetFactory');
        $prop = $refl->getProperty('workers');
        $prop->setAccessible(true);

        $this->assertCount(1, $prop->getValue($factory));
    }
}

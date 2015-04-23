<?php

namespace Spiffy\Assetic\Plugin;

use Spiffy\Assetic\AsseticService;
use Spiffy\Event\Event;
use Spiffy\Event\EventManager;

/**
 * @coversDefaultClass \Spiffy\Assetic\Plugin\AssetLoaderPlugin
 */
class AssetLoaderPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::plug
     */
    public function testPlug()
    {
        $manager = new EventManager();

        $p = new AssetLoaderPlugin([]);
        $p->plug($manager);

        $this->assertCount(1, $manager->getEvents(AsseticService::EVENT_LOAD));
    }

    /**
     * @covers ::__construct
     * @covers ::onLoad
     */
    public function testOnLoad()
    {
        $config = [
            'valid' => ['inputs' => ['foo', 'bar']],
            'invalid' => '',
        ];

        $service = new AsseticService(__DIR__ . '/../');
        $manager = $service->getAssetManager();

        $e = new Event();
        $e->setTarget($service);

        $p = new AssetLoaderPlugin($config);
        $p->onLoad($e);

        $this->assertTrue($manager->has('valid'));
        $this->assertFalse($manager->has('invalid'));
    }
}

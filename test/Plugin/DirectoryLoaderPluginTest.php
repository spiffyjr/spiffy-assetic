<?php

namespace Spiffy\Assetic\Plugin;

use Spiffy\Assetic\AsseticService;
use Spiffy\Event\Event;
use Spiffy\Event\EventManager;

/**
 * @coversDefaultClass \Spiffy\Assetic\Plugin\DirectoryLoaderPlugin
 */
class DirectoryLoaderPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::plug
     */
    public function testPlug()
    {
        $manager = new EventManager();

        $p = new DirectoryLoaderPlugin([], sys_get_temp_dir());
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
            'css' => [
                [__DIR__ . '/../asset/css']
            ],
            'invalid' => '',
            'also_invalid' => [[]]
        ];

        $service = new AsseticService(__DIR__ . '/../');
        $manager = $service->getAssetManager();

        $e = new Event();
        $e->setTarget($service);

        $p = new DirectoryLoaderPlugin($config, sys_get_temp_dir());
        $p->onLoad($e);

        $resources = $manager->getResources();
        $this->assertCount(1, $resources);
        $this->assertInstanceOf('Spiffy\Assetic\Assetic\RecursiveDirectoryResource', $resources[0]);
    }
}

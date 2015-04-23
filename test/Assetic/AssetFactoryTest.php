<?php

namespace Spiffy\Assetic\Assetic;

use Assetic\Asset\StringAsset;
use Assetic\AssetManager;
use Spiffy\Assetic\AsseticService;

/**
 * @coversDefaultClass \Spiffy\Assetic\Assetic\AssetFactory
 */
class AssetFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::parseInput
     */
    public function testParseInputIsProxiedThroughResolveAlias()
    {
        $am = new AssetManager();
        $am->set('foo', new StringAsset('string'));

        $service = new AsseticService(__DIR__);
        $service->events()->on(AsseticService::EVENT_RESOLVE_ALIAS, function ($e) {
            $e->setTarget('@foo');
        });

        $factory = new AssetFactory($service);
        $factory->setAssetManager($am);
        $asset = $factory->createAsset(['inputs' => ['@__foo']]);

        $this->assertInstanceOf('Assetic\Asset\AssetCollection', $asset);
    }
}

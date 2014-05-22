<?php

namespace Spiffy\Assetic
{
    use Assetic\Asset\AssetCollection;
    use Assetic\Asset\FileAsset;
    use Assetic\Asset\StringAsset;

    /**
     * Helper functions to override the default functionality of file_get_contents.
     * PHP YOU BE CRAY CRAY.
     */
    function file_put_contents($filename, $data, $flags = null, $context = null)
    {
        if (AsseticServiceTest::$forceWriteFailures) {
            return false;
        }
        return \file_put_contents($filename, $data, $flags, $context);
    }

    function mkdir($pathname, $mode = 0777, $recursive = false)
    {
        if (AsseticServiceTest::$forceWriteFailures) {
            return false;
        }
        return \mkdir($pathname, $mode, $recursive);
    }

    /**
     * @coversDefaultClass \Spiffy\Assetic\AsseticService
     */
    class AsseticServiceTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @var bool
         */
        public static $forceWriteFailures = false;

        /**
         * @var FileAsset
         */
        protected $asset;

        /**
         * @var AssetCollection
         */
        protected $collection;

        /**
         * @var string
         */
        protected $outputDir;

        /**
         * @var AsseticService
         */
        protected $service;

        /**
         * @covers ::__construct, ::isLoaded
         */
        public function testIsLoaded()
        {
            $s = $this->service;
            $this->assertFalse($s->isLoaded());

            $s->load();
            $this->assertTrue($s->isLoaded());

            $s->load();
            $this->assertTrue($s->isLoaded());
        }

        /**
         * @covers ::writeAssets, ::doDump
         */
        public function testWriteAssets()
        {
            $s = $this->service;
            $s->writeAssets($this->outputDir);

            $this->assertFileExists($this->outputDir . '/template.twig');
        }

        /**
         * @covers ::writeAsset, ::doDump
         */
        public function testWriteAsset()
        {
            $s = $this->service;
            $s->writeAsset('foo', $this->outputDir);

            $this->assertFileExists($this->outputDir . '/template.twig');
        }

        /**
         * @covers ::writeAsset, ::doDump
         */
        public function testWriteAssetWithDebugMode()
        {
            $s = $this->service;
            $af = $s->getAssetFactory();
            $af->setDebug(true);

            $s->writeAsset('foo', $this->outputDir);
            $s->writeAsset('bar', $this->outputDir);

            $this->assertFileExists($this->outputDir . '/template.twig');
            $this->assertFileExists($this->outputDir . '/collection.css');
            $this->assertFileExists($this->outputDir . '/collection_bootstrap.min_1.css');
            $this->assertFileExists($this->outputDir . '/collection_embed_2.css');
        }

        /**
         * @covers ::writeAsset, ::doDump
         */
        public function testWriteAssetWithVerbose()
        {
            $s = $this->service;
            $af = $s->getAssetFactory();
            $af->setDebug(true);

            $s->writeAsset('foo', $this->outputDir, [], true);
            $s->writeAsset('bar', $this->outputDir, [], true);

            $this->assertFileExists($this->outputDir . '/template.twig');
            $this->assertFileExists($this->outputDir . '/collection.css');
            $this->assertFileExists($this->outputDir . '/collection_bootstrap.min_1.css');
            $this->assertFileExists($this->outputDir . '/collection_embed_2.css');
        }

        /**
         * @covers ::doDump
         */
        public function testDoDumpTriggersEvents()
        {
            $dirCount = 0;
            $assetCount = 0;

            $s = $this->service;
            $s->events()->on(AsseticService::EVENT_DUMP_DIR, function ($e) use (&$dirCount) {
                $dirCount++;
            });

            $s->events()->on(AsseticService::EVENT_DUMP_ASSET, function ($e) use (&$assetCount) {
                $assetCount++;
            });

            $s->writeAsset('foo', $this->outputDir, [], true);
            $s->writeAsset('bar', $this->outputDir, [], true);

            $this->assertSame(1, $dirCount);
            $this->assertSame(3, $assetCount);
        }

        /**
         * @cover ::doDump
         * @expectedException \RuntimeException
         * @expectedExceptionMessage Unable to write file
         */
        public function testDoDumpThrowsExceptionWhenUnableToWriteFile()
        {
            self::$forceWriteFailures = true;

            $s = $this->service;
            $am = $s->getAssetManager();

            $asset = new StringAsset('someasset');
            $asset->setTargetPath('somefile.txt');

            $am->set('baz', $asset);
            $s->writeAsset('baz', '');
        }

        /**
         * @cover ::doDump
         * @expectedException \RuntimeException
         * @expectedExceptionMessage Unable to create directory /foo
         */
        public function testDoDumpThrowsExceptionWhenUnableToWriteDirectory()
        {
            self::$forceWriteFailures = true;

            $s = $this->service;
            $am = $s->getAssetManager();

            $asset = new StringAsset('someasset');
            $asset->setTargetPath('foo/somefile.txt');

            $am->set('baz', $asset);
            $s->writeAsset('baz', '');
        }

        /**
         * @covers ::clear
         */
        public function testClear()
        {
            $s = $this->service;
            $am = $s->getAssetManager();

            $s->load();
            $this->assertTrue($s->isLoaded());
            $this->assertTrue($am->has('foo'));

            $s->clear();
            $this->assertFalse($s->isLoaded());
            $this->assertFalse($am->has('foo'));
        }

        /**
         * @covers ::checkAsset
         */
        public function testCheckAsset()
        {
            $s = $this->service;
            $previous = [];

            $this->assertTrue($s->checkAsset('foo', [], $previous));
            $this->assertFalse($s->checkAsset('foo', [], $previous));
        }

        protected function setUp()
        {
            $this->asset = new FileAsset(__DIR__ . '/asset/template.twig');
            $this->asset->setTargetPath('template.twig');

            $this->collection = new AssetCollection([
                new FileAsset(__DIR__ . '/asset/css/bootstrap.min.css'),
                new FileAsset(__DIR__ . '/asset/css/embed.css')
            ]);
            $this->collection->setTargetPath('collection.css');

            $this->service = new AsseticService(__DIR__ . '/../');
            $this->service->getAssetManager()->set('foo', $this->asset);
            $this->service->getAssetManager()->set('bar', $this->collection);

            $this->outputDir = sys_get_temp_dir() . '/assetic';
        }

        protected function tearDown()
        {
            self::$forceWriteFailures = false;

            if (file_exists($this->outputDir . '/template.twig')) {
                unlink($this->outputDir . '/template.twig');
            }
            if (file_exists($this->outputDir . '/collection.css')) {
                unlink($this->outputDir . '/collection.css');
            }
            if (file_exists($this->outputDir . '/collection_bootstrap.min_1.css')) {
                unlink($this->outputDir . '/collection_bootstrap.min_1.css');
            }
            if (file_exists($this->outputDir . '/collection_embed_2.css')) {
                unlink($this->outputDir . '/collection_embed_2.css');
            }
            if (file_exists($this->outputDir)) {
                rmdir($this->outputDir);
            }
        }
    }
}

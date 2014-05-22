<?php

namespace Spiffy\Assetic\Assetic;

use Assetic\Factory\AssetFactory;
use Assetic\Factory\Resource\FileResource;

/**
 * @coversDefaultClass \Spiffy\Assetic\Assetic\DirectoryFormulaLoaderTest
 */
class DirectoryFormulaLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DirectoryFormulaLoader
     */
    protected $loader;

    /**
     * @var string
     */
    protected $tmp;

    /**
     * @covers ::__construct, ::load
     * @expectedException \Spiffy\Assetic\Assetic\Exception\InvalidResourceException
     * @expectedExceptionMessage RecursiveDirectoryFormulaLoader expects RecursiveDirectoryResources
     */
    public function testLoadThrowsExceptionWithInvalidResource()
    {
        $l = $this->loader;
        $l->load(new FileResource(__DIR__ . '/AssetFactory.php'));
    }

    /**
     * @covers ::__construct, ::load, ::convertSeparators
     */
    public function testLoadWithAbsolutePaths()
    {
        $l = $this->loader;
        $dir = realpath(__DIR__ . '/../asset');

        $resource = new RecursiveDirectoryResource($dir, '/bootstrap.min.(?:js|css)$/');

        $result = $l->load($resource);
        $keys = array_keys($result);
        sort($result[$keys[0]]);
        sort($result[$keys[1]]);

        $this->assertSame([
            0 => [],
            1 => [$dir . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'bootstrap.min.css'],
            2 => ['output' => $this->tmp . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'bootstrap.min.css']
        ], $result[$keys[0]]);

        $this->assertSame([
            0 => [],
            1 => [$dir . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'bootstrap.min.js'],
            2 => ['output' => $this->tmp . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'bootstrap.min.js']
        ], $result[$keys[1]]);
    }

    /**
     * @covers ::__construct, ::load, ::convertSeparators
     */
    public function testLoadWithRelativePaths()
    {
        $l = $this->loader;
        $dir = realpath(__DIR__ . '/../asset');
        $curdir = getcwd();
        chdir(__DIR__ . '/../');

        $resource = new RecursiveDirectoryResource('asset', '/bootstrap.min.(?:js|css)$/');

        $result = $l->load($resource);
        $keys = array_keys($result);
        sort($result[$keys[0]]);
        sort($result[$keys[1]]);

        $this->assertSame([
            0 => [],
            1 => [$dir . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'bootstrap.min.css'],
            2 => ['output' => $this->tmp . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'bootstrap.min.css']
        ], $result[$keys[0]]);

        next($result);

        $this->assertSame([
            0 => [],
            1 => [$dir . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'bootstrap.min.js'],
            2 => ['output' => $this->tmp . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'bootstrap.min.js']
        ], $result[$keys[1]]);

        chdir($curdir);
    }

    protected function setUp()
    {
        $this->tmp = $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'assetic' . DIRECTORY_SEPARATOR . 'output';

        $this->loader = new DirectoryFormulaLoader(
            new AssetFactory(__DIR__ . '/../'),
            $tmp,
            []
        );
    }
}

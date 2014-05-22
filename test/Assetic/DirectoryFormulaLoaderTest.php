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

        $this->assertSame([
            0 => [$dir . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'bootstrap.min.css'],
            1 => [],
            2 => ['output' => $this->tmp . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'bootstrap.min.css']
        ], current($result));

        next($result);

        $this->assertSame([
            0 => [$dir . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'bootstrap.min.js'],
            1 => [],
            2 => ['output' => $this->tmp . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'bootstrap.min.js']
        ], current($result));
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

        $this->assertSame([
            0 => [$dir . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'bootstrap.min.css'],
            1 => [],
            2 => ['output' => $this->tmp . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'bootstrap.min.css']
        ], current($result));

        next($result);

        $this->assertSame([
            0 => [$dir . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'bootstrap.min.js'],
            1 => [],
            2 => ['output' => $this->tmp . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'bootstrap.min.js']
        ], current($result));

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

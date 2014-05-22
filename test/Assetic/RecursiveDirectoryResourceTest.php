<?php

namespace Spiffy\Assetic\Assetic;
use Symfony\Component\Finder\Finder;

/**
 * @coversDefaultClass \Spiffy\Assetic\Assetic\RecursiveDirectoryResource
 */
class RecursiveDirectoryResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RecursiveDirectoryResource
     */
    protected $resource;

    /**
     * @covers ::__construct, ::getPath
     */
    public function testGetPath()
    {
        $r = $this->resource;
        $this->assertSame(realpath(__DIR__ . '/../asset'), $r->getPath());
    }

    /**
     * @covers ::__construct, ::__toString
     */
    public function testToString()
    {
        $r = $this->resource;
        $this->assertSame($r->getPath(), (string) $r);
    }

    /**
     * @covers ::__construct, getContent
     */
    public function testGetContent()
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in(realpath(__DIR__ . '/../asset'))
            ->name('*');

        $content = $this->resource->getContent();

        $this->assertInstanceOf('Symfony\Component\Finder\Finder', $content);
        $this->assertEquals($finder, $content);
    }

    /**
     * @covers ::__construct, ::isFresh
     */
    public function testIsFresh()
    {
        $r = $this->resource;
        $this->assertFalse($r->isFresh(0));
        $this->assertFalse($r->isFresh(null));
        $this->assertFalse($r->isFresh(1329875298734));
    }

    protected function setUp()
    {
        $this->resource = new RecursiveDirectoryResource(__DIR__ . '/../asset', '*');
    }
}

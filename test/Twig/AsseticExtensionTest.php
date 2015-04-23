<?php

namespace Spiffy\Assetic\Twig;
use Assetic\Factory\AssetFactory;

/**
 * @coversDefaultClass \Spiffy\Assetic\Twig\AsseticExtension
 */
class AsseticExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getTokenParsers
     */
    public function testGetTokenParsers()
    {
        $factory = new AssetFactory(__DIR__ . '/../');
        $ext = new AsseticExtension(
            $factory,
            [
                'javascripts' => ['tag' => 'javascripts', 'output' => 'js/*.js'],
                'stylesheets' => ['tag' => 'stylesheets', 'output' => 'css/*.css'],
                'image' => ['tag' => 'image', 'output' => 'image/*', 'single' => true],
            ]
        );

        $parsers = $ext->getTokenParsers();
        $this->assertCount(3, $parsers);

        $js = $parsers[0];
        $css = $parsers[1];
        $img = $parsers[2];

        $this->assertInstanceOf('Assetic\Extension\Twig\AsseticTokenParser', $js);
        $this->assertInstanceOf('Assetic\Extension\Twig\AsseticTokenParser', $css);
        $this->assertInstanceOf('Assetic\Extension\Twig\AsseticTokenParser', $img);

        $this->assertSame('javascripts', $js->getTag());
        $this->assertSame('stylesheets', $css->getTag());
        $this->assertSame('image', $img->getTag());
    }
}

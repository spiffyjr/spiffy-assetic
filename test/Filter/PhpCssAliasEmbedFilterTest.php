<?php

namespace Spiffy\Assetic\Filter;

use Assetic\Asset\FileAsset;
use Spiffy\Assetic\AsseticService;
use Spiffy\Event\Event;

/**
 * @coversDefaultClass \Spiffy\Assetic\Filter\PhpCssAliasEmbedFilter
 */
class PhpCssAliasEmbedFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhpCssAliasEmbedFilter
     */
    protected $filter;

    /**
     * @var AsseticService
     */
    protected $service;

    /**
     * @covers ::__construct, ::filterDump
     */
    public function testResolveAliasModifiesContent()
    {
        $asset = new FileAsset(__DIR__ . '/../asset/css/embed.css');
        $asset->load();

        $s = $this->service;
        // replaces @asset with the real directory
        $s->events()->on(AsseticService::EVENT_RESOLVE_ALIAS, function (Event $e) {
            $e->setTarget(str_replace('@asset', realpath(__DIR__ . '/../asset'), $e->getTarget()));
        });

        $f = $this->filter;
        $f->filterDump($asset);

        $expected = <<<EXPECTED
body {
    background: url(data:image/png;base64,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);
}

EXPECTED;

        $this->assertSame($expected, $asset->getContent());
    }

    /**
     * @covers ::__construct, ::filterDump
     */
    public function testFilterDumpSkipsIfResolveDoesNotModifyAlias()
    {
        $this->setExpectedException(
            'PHPUnit_Framework_Error',
            'mime_content_type(' . __DIR__ . '/../asset/css/@asset/image/embed.png): ' .
            'failed to open stream: No such file or directory');

        $asset = new FileAsset(__DIR__ . '/../asset/css/embed.css');
        $asset->load();

        $content = $asset->getContent();

        $f = $this->filter;
        $f->filterDump($asset);

        $this->assertSame($content, $asset->getContent());
    }

    protected function setUp()
    {
        $this->service = $service = new AsseticService(__DIR__ . '/../');

        $this->filter = new PhpCssAliasEmbedFilter($service);
    }
}

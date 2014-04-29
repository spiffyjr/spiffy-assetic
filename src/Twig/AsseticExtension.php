<?php

namespace Spiffy\Assetic\Twig;

use Assetic\Extension\Twig\AsseticExtension as BaseAsseticExtension;
use Assetic\Extension\Twig\AsseticTokenParser;

class AsseticExtension extends BaseAsseticExtension
{
    /**
     * @return array
     */
    public function getTokenParsers()
    {
        return array(
            new AsseticTokenParser($this->factory, 'javascripts', 'asset/js/*.js'),
            new AsseticTokenParser($this->factory, 'stylesheets', 'asset/css/*.css'),
            new AsseticTokenParser($this->factory, 'image', 'asset/image/*', true),
        );
    }
}

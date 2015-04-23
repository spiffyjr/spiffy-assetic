<?php

namespace Spiffy\Assetic\Twig;

use Assetic\Extension\Twig\AsseticExtension as BaseAsseticExtension;
use Assetic\Extension\Twig\AsseticTokenParser;
use Assetic\Factory\AssetFactory;
use Assetic\ValueSupplierInterface;

class AsseticExtension extends BaseAsseticExtension
{
    /**
     * @var array
     */
    protected $parsers;

    /**
     * @param AssetFactory $factory
     * @param array $parsers
     * @param array $functions
     * @param ValueSupplierInterface $valueSupplier
     */
    public function __construct(
        AssetFactory $factory,
        $parsers,
        $functions = [],
        ValueSupplierInterface $valueSupplier = null
    ) {
        $this->parsers = $parsers;

        parent::__construct($factory, $functions, $valueSupplier);
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenParsers()
    {
        $parsers = [];
        foreach ($this->parsers as $spec) {
            $parsers[] = new AsseticTokenParser(
                $this->factory,
                $spec['tag'],
                $spec['output'],
                isset($spec['single']) ? $spec['single'] : false
            );
        }

        return $parsers;
    }
}

<?php

namespace Spiffy\Assetic\Loader;

use Assetic\Factory\Resource\ResourceInterface;
use Symfony\Component\Finder\Finder;

class RecursiveDirectoryResource implements ResourceInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var int
     */
    protected $flags;

    /**
     * @param string $path
     * @param string $pattern
     */
    public function __construct($path, $pattern = '.*')
    {
        $this->path = $path;
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritDoc}
     */
    public function isFresh($timestamp)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @return Finder
     */
    public function getContent()
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in($this->path)
            ->name($this->pattern);

        return $finder;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->path;
    }
}

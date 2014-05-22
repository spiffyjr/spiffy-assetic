<?php

namespace Spiffy\Assetic\Assetic;

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
    public function __construct($path, $pattern = '*')
    {
        $this->path = realpath($path);
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
     * TODO: implement me. Iterate through files found in finder and check if modified?
     * @param int $timestamp
     * @return false
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

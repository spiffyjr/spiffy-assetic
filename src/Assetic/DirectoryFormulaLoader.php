<?php

namespace Spiffy\Assetic\Assetic;

use Assetic\Factory\Loader\FormulaLoaderInterface;
use Assetic\Factory\Resource\ResourceInterface;
use Symfony\Component\Finder\Finder;

class DirectoryFormulaLoader implements FormulaLoaderInterface
{
    /**
     * @var AssetFactory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var string
     */
    protected $output;

    /**
     * @param AssetFactory $factory
     * @param string $output
     * @param array $filters
     */
    public function __construct(AssetFactory $factory, $output, array $filters = [])
    {
        $this->factory = $factory;
        $this->output = $output;
        $this->filters = $filters;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ResourceInterface $resource)
    {
        if (!$resource instanceof RecursiveDirectoryResource) {
            throw new \RuntimeException('RecursiveDirectoryFormulaLoader only works with RecursiveDirectoryResources');
        }

        $finder = $resource->getContent();
        $path = $resource->getPath();
        $result = [];

        foreach ($finder as $file) {
            /** @var \Symfony\Component\Finder\SplFileinfo $file */
            $name = $this->factory->generateAssetName($file->getRealPath(), $this->filters);
            $replaceRegex = sprintf('@^.*%s\/?@', $path);
            $result[$name] = [
                [$file->getRealPath()],
                $this->filters,
                [
                    'output' => $this->output . '/' . preg_replace($replaceRegex, '', $file->getRealpath())
                ]
            ];
        }

        return $result;
    }
}

<?php

namespace Spiffy\Assetic\Assetic;

use Assetic\Factory\AssetFactory as BaseAssetFactory;
use Assetic\Factory\Loader\FormulaLoaderInterface;
use Assetic\Factory\Resource\ResourceInterface;
use Spiffy\Assetic\Assetic\Exception\InvalidResourceException;
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
     * @param BaseAssetFactory $factory
     * @param string $output
     * @param array $filters
     */
    public function __construct(BaseAssetFactory $factory, $output, array $filters = [])
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
            throw new InvalidResourceException(
                'RecursiveDirectoryFormulaLoader expects RecursiveDirectoryResources'
            );
        }

        $finder = $resource->getContent();
        $result = [];

        foreach ($finder as $file) {
            /** @var \Symfony\Component\Finder\SplFileinfo $file */
            $name = $this->factory->generateAssetName($file->getRealPath(), $this->filters);
            $output = $this->output . str_replace($resource->getPath(), '', $file->getRealpath());

            $result[$name] = [
                [$file->getRealPath()],
                $this->filters,
                ['output' => $this->convertSeparators($output)]
            ];
        }

        return $result;
    }

    /**
     * @param string $input
     * @return string
     */
    protected function convertSeparators($input)
    {
        return preg_replace('@[\\/]@', DIRECTORY_SEPARATOR, $input);
    }
}

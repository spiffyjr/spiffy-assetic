<?php

namespace Spiffy\Assetic\Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Spiffy\Assetic\Assetic\AssetFactory;

/**
 * Filter to convert url:@ModuleName to absolute path. Intended to be combined with the CssEmbed filter to
 * embed images using absolute module paths.
 */
class CssModulePathFilter implements FilterInterface
{
    /**
     * @var \Spiffy\Assetic\Assetic\AssetFactory
     */
    protected $factory;

    /**
     * @param AssetFactory $factory
     */
    public function __construct(AssetFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param AssetInterface $asset
     */
    public function filterLoad(AssetInterface $asset)
    {
        $this->filter($asset);
    }

    /**
     * @param AssetInterface $asset
     */
    public function filterDump(AssetInterface $asset)
    {
        $this->filter($asset);
    }

    /**
     * @param AssetInterface $asset
     */
    protected function filter(AssetInterface $asset)
    {
        $content = $asset->getContent();

        $replace = function ($match) use ($asset) {
            $relativePath = $this->findRelativePath(
                $asset->getSourceRoot(),
                $this->factory->convertModuleInput($match[2])
            );

            return $match[1] . $relativePath . '/';
        };

        $asset->setContent(preg_replace_callback('/(url\(\s*\'?)(@\w+\/)/', $replace, $content));
    }

    /**
     * @param string $fromPath
     * @param string $toPath
     * @return string
     */
    protected function findRelativePath($fromPath, $toPath)
    {
        $from = explode(DIRECTORY_SEPARATOR, $fromPath);
        $to = explode(DIRECTORY_SEPARATOR, $toPath);
        $relpath = '';

        $i = 0;
        // find how far the path is the same
        while ( isset($from[$i]) && isset($to[$i]) ) {
            if ( $from[$i] != $to[$i] ) break;
            $i++;
        }
        $j = count( $from ) - 1;
        // add '..' until the path is the same
        while ( $i <= $j ) {
            if ( !empty($from[$j]) ) $relpath .= '..'.DIRECTORY_SEPARATOR;
            $j--;
        }
        // go to folder from where it starts differing
        while ( isset($to[$i]) ) {
            if ( !empty($to[$i]) ) $relpath .= $to[$i].DIRECTORY_SEPARATOR;
            $i++;
        }

        // strip last separator
        return substr($relpath, 0, -1);
    }
}

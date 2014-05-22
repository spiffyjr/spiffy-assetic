<?php

namespace Spiffy\Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\PhpCssEmbedFilter;
use Spiffy\Assetic\AsseticService;

/**
 * Filter to convert url:@ModuleName to absolute path. Intended to be combined with the CssEmbed filter to
 * embed images using absolute module paths.
 */
class PhpCssAliasEmbedFilter extends PhpCssEmbedFilter
{
    /**
     * @var AsseticService
     */
    protected $asseticService;

    /**
     * @param AsseticService $asseticService
     */
    public function __construct(AsseticService $asseticService)
    {
        $this->asseticService = $asseticService;
    }

    /**
     * @param AssetInterface $asset
     * @codeCoverageIgnore
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * @param AssetInterface $asset
     */
    public function filterDump(AssetInterface $asset)
    {
        $this->resolve($asset);
        try {
            parent::filterLoad($asset);
        } catch (\InvalidArgumentException $e) {
        }
    }

    /**
     * @param AssetInterface $asset
     */
    protected function resolve(AssetInterface $asset)
    {
        $content = $asset->getContent();

        $replace = function ($match) use ($asset) {
            $aliasPath = $this->asseticService->resolveAlias(
                $match[2],
                ['source' => 'css']
            );

            if ($aliasPath == $match[2]) {
                return $match[0];
            }

            return $match[1] . $this->findRelativePath($asset->getSourceRoot(), $aliasPath);
        };

        // aliases can be marked with @ or $
        // e.g., @MyModule, @MyPackage, $MyVar
        // the result of the match is handled by resolve alias plugins
        $asset->setContent(preg_replace_callback('/(url\(\s*[\'"]?)([@$][^\'")]+)/', $replace, $content));
    }

    /**
     * @see http://stackoverflow.com/questions/2637945/getting-relative-path-from-absolute-path-in-php
     *
     * @param string $fromPath
     * @param string $toPath
     * @return string
     */
    protected function findRelativePath($fromPath, $toPath)
    {
        $from = explode(DIRECTORY_SEPARATOR, realpath($fromPath));
        $to = explode(DIRECTORY_SEPARATOR, realpath($toPath));
        $relpath = '';

        $i = 0;
        // find how far the path is the same
        while (isset($from[$i]) && isset($to[$i])) {
            if ($from[$i] != $to[$i]) {
                break;
            }
            $i++;
        }
        $j = count($from) - 1;
        // add '..' until the path is the same
        while ($i <= $j) {
            if (!empty($from[$j])) {
                $relpath .= '..'.DIRECTORY_SEPARATOR;
            }
            $j--;
        }
        // go to folder from where it starts differing
        while (isset($to[$i])) {
            if (!empty($to[$i])) {
                $relpath .= $to[$i].DIRECTORY_SEPARATOR;
            }
            $i++;
        }

        // strip last separator
        return substr($relpath, 0, -1);
    }
}

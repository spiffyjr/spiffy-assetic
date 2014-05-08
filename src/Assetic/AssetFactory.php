<?php

namespace Spiffy\Assetic\Assetic;

use Assetic\Factory\AssetFactory as BaseAssetFactory;
use Spiffy\Assetic\AsseticService;

class AssetFactory extends BaseAssetFactory
{
    /**
     * @param AsseticService $asseticService
     */
    public function __construct(AsseticService $asseticService)
    {
        $this->asseticService = $asseticService;

        parent::__construct($asseticService->getRoot(), $asseticService->isDebug());
    }

    /**
     * {@inheritDoc}
     */
    protected function parseInput($input, array $options = [])
    {
        return parent::parseInput($this->asseticService->resolveAlias($input), $options);
    }
}

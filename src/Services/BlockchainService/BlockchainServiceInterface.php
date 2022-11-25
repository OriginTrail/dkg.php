<?php

namespace Dkg\Services\BlockchainService;

use Dkg\Services\BlockchainService\Dto\BlockchainConfig;
use Dkg\Services\BlockchainService\Dto\Asset;

interface BlockchainServiceInterface
{
    public function createAsset(Asset $asset, float $tokenAmount, BlockchainConfig $config);
}

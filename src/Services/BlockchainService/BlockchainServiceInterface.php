<?php

namespace Dkg\Services\BlockchainService;

use Dkg\Services\AssetService\Dto\PublishOptions;
use Dkg\Services\BlockchainService\Dto\Asset;

interface BlockchainServiceInterface
{
    public function createAsset(Asset $asset, PublishOptions $options);
}

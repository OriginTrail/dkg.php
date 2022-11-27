<?php

namespace Dkg\Services\BlockchainService;

use Dkg\Exceptions\BlockchainException;
use Dkg\Exceptions\ConfigMissingException;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\PublishOptions;

interface BlockchainServiceInterface
{
    /**
     * Creates asset on blockchain.
     * @param Asset $asset
     * @param PublishOptions $options
     * @return string UAI
     * @throws BlockchainException
     * @throws ConfigMissingException
     */
    public function createAsset(Asset $asset, PublishOptions $options): string;
}

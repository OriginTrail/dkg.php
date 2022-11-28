<?php

namespace Dkg\Services\BlockchainService;

use Dkg\Exceptions\BlockchainException;
use Dkg\Exceptions\ServiceMisconfigurationException;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\PublishOptions;

interface BlockchainServiceInterface
{
    /**
     * Creates asset on blockchain.
     * @param Asset $asset
     * @param PublishOptions $options
     * @return Asset asset with enhanced with blockchainName, contractAddress and $tokenId
     * @throws BlockchainException
     * @throws ServiceMisconfigurationException
     */
    public function createAsset(Asset $asset, PublishOptions $options): Asset;
}

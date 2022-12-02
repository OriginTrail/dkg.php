<?php

namespace Dkg\Services\BlockchainService;

use Dkg\Exceptions\BlockchainException;
use Dkg\Exceptions\ServiceMisconfigurationException;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\PublishOptions;
use Dkg\Services\BlockchainService\Dto\BlockchainConfig;

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

    /**
     * @param int $tokenId
     * @param BlockchainConfig $config
     * @return mixed
     */
    public function getLatestAssertionId(int $tokenId, BlockchainConfig $config);

    /**
     * @param Asset $asset
     * @param PublishOptions|null $options
     */
    public function updateAsset(Asset $asset, ?PublishOptions $options);

    /**
     * Merged request and base configuration
     * @param BlockchainConfig|null $config
     * @return BlockchainConfig
     */
    public function getMergedConfig(?BlockchainConfig $config): BlockchainConfig;

    /**
     * @param string $tokenId
     * @param BlockchainConfig|null $config
     * @return string owner address
     */
    public function getAssetOwner(string $tokenId, ?BlockchainConfig $config = null): ?string;

    /**
     * @param string|null $tokenId
     * @param string $toAddress
     * @param BlockchainConfig|null $blockchainConfig
     * @return mixed
     */
    public function transferAsset(?string $tokenId, string $toAddress, ?BlockchainConfig $blockchainConfig);
}

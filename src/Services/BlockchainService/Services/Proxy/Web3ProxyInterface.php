<?php

namespace Dkg\Services\BlockchainService\Services\Proxy;

use Dkg\Exceptions\BlockchainException;
use Dkg\Services\BlockchainService\Dto\BlockchainConfig;
use Dkg\Services\BlockchainService\Services\Proxy\Dto\BlockchainInfo;

interface Web3ProxyInterface
{
    /**
     * Returns newly initialized Web3ProxyInterface instance.
     * @param BlockchainInfo $blockchainInfo
     * @return Web3ProxyInterface
     */
    public static function init(BlockchainInfo $blockchainInfo): Web3ProxyInterface;

    /**
     * Triggers increaseAllowance function on the contract.
     * @param float $amount bid amount in ether
     * @param BlockchainConfig $config
     * @return mixed
     * @throws BlockchainException
     */
    public function increaseAllowance(float $amount, BlockchainConfig $config);

    /**
     * @param array $args
     * @param BlockchainConfig $config
     * @return array [string assetContract, BigInteger tokenId, string stateCommitHash]
     * @throws BlockchainException
     */
    public function createAsset(array $args, BlockchainConfig $config): array;

    /**
     * @param int $tokenId
     * @return string
     */
    public function getLatestAssertionId(int $tokenId): string;

    public function updateAsset(array $args, BlockchainConfig $config);
}

<?php

namespace Dkg\Services\BlockchainService\Proxy;

use Dkg\Services\BlockchainService\Proxy\Dto\BlockchainInfo;

interface Web3ProxyInterface
{
    /**
     * Returns newly initialized Web3ProxyInterface instance
     * @param BlockchainInfo $blockchainInfo
     * @return Web3ProxyInterface
     */
    public static function init(BlockchainInfo $blockchainInfo): Web3ProxyInterface;

    /**
     * Triggers increaseAllowance function on the contract
     * @param float $amount
     * @param string $publicKey
     * @param string $privateKey
     * @return mixed
     */
    public function increaseAllowance(float $amount, string $publicKey, string $privateKey);

}

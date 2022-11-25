<?php

namespace Dkg\Services\BlockchainService\Proxy;

interface Web3ProxyManagerInterface
{
    /**
     * Returns singleton instance
     * @return Web3ProxyManager
     */
    public static function getInstance(): Web3ProxyManagerInterface;

    /**
     * Initializes proxy inside of manager
     * @param string $blockchainName
     * @return void
     */
    public function initializeProxy(string $blockchainName);

    /**
     * Returns initialized proxy
     * @param string $blockchainName
     * @return Web3ProxyInterface
     */
    public function getProxy(string $blockchainName): Web3ProxyInterface;
}

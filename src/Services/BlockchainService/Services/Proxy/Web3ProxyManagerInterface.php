<?php

namespace Dkg\Services\BlockchainService\Services\Proxy;

interface Web3ProxyManagerInterface
{
    /**
     * Returns singleton instance
     * @return Web3ProxyManager
     */
    public static function getInstance(): Web3ProxyManagerInterface;

    /**
     * Initializes proxy inside of manager.
     * @param string $blockchainName
     * @return void
     */
    public function initializeProxy(string $blockchainName);

    /**
     * Returns initialized proxy.
     * If proxy is not previously initialized it will get initialized and returned.
     * @param string $blockchainName
     * @return Web3ProxyInterface
     */
    public function getProxy(string $blockchainName): Web3ProxyInterface;
}

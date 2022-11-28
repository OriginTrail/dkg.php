<?php

namespace Dkg\Services\BlockchainService\Services\Proxy;

interface Web3ProxyManagerInterface
{
    /**
     * @return Web3ProxyManagerInterface
     */
    public static function getInstance(): Web3ProxyManagerInterface;

    /**
     * Returns initialized proxy.
     * If proxy is not previously initialized it will get initialized and returned.
     * @param string $blockchainName
     * @return Web3ProxyInterface
     */
    public function getProxy(string $blockchainName): Web3ProxyInterface;
}

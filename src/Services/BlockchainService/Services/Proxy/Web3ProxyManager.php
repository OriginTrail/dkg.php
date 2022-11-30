<?php

namespace Dkg\Services\BlockchainService\Services\Proxy;

use Dkg\Services\BlockchainService\Dto\BlockchainConfig;
use Dkg\Services\BlockchainService\Services\Proxy\Dto\BlockchainInfo;
use InvalidArgumentException;

class Web3ProxyManager implements Web3ProxyManagerInterface
{
    /** @var BlockchainInfo[] */
    private $infoMap = [];

    /** @var BlockchainConfig */
    private $defaultConfig;

    /** @var Web3ProxyInterface[] */
    private $proxyMap = [];

    /** @var Web3ProxyManagerInterface */
    private static $instance;

    private function __construct()
    {
        $this->initializeInfoMap();
    }

    public static function getInstance(): Web3ProxyManagerInterface
    {
        if (!self::$instance) {
            self::$instance = new Web3ProxyManager();
        }

        return self::$instance;
    }

    /**
     * Initializes blockchain proxy and stores it in a proxy map
     * @param string $blockchainName
     * @return void
     * @throws InvalidArgumentException Provided blockchain doesn't exist.
     */
    private function initializeProxy(string $blockchainName)
    {
        $blockchainInfo = $this->infoMap[$blockchainName];

        if (!$blockchainInfo) {
            throw new InvalidArgumentException("Provided blockchain '$blockchainName' is not supported.");
        }

        if (!isset($this->proxyMap[$blockchainName])) {
            $proxy = Web3Proxy::init($blockchainInfo);
            $this->proxyMap[$blockchainName] = $proxy;
        }
    }

    /**
     * Returns proxy for passed blockchain.
     * If proxy is not initialized, it will get initialized and returned.
     * @param string $blockchainName
     * @return Web3ProxyInterface
     */
    public function getProxy(string $blockchainName): Web3ProxyInterface
    {
        if (!isset($this->proxyMap[$blockchainName])) {
            $this->initializeProxy($blockchainName);
        }

        return $this->proxyMap[$blockchainName];
    }

    /**
     * @return string[]
     */
    public function getAvailableBlockchains(): array
    {
        return array_keys($this->infoMap);
    }

    private function initializeInfoMap(): void
    {
        $this->infoMap['ganache'] = new BlockchainInfo(
            'ganache',
            'http://localhost:7545',
            '0x209679fA3B658Cd0fC74473aF28243bfe78a9b12',
            1337
        );
        $this->infoMap['polygon'] = new BlockchainInfo(
            'polygon',
            'https://matic-mumbai.chainstacklabs.com',
            '0xdaa16AC171CfE8Df6F79C06E7EEAb2249E2C9Ec8',
            137
        );
        $this->infoMap['otp'] = new BlockchainInfo(
            'otp',
            'https://lofar.origintrail.network',
            '0xc9184C1A0CE150a882DC3151Def25075bdAf069C',
            2160
        );
    }
}

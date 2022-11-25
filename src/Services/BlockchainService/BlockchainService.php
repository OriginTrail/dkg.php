<?php

namespace Dkg\Services\BlockchainService;

use Dkg\Exceptions\ConfigMissingException;
use Dkg\Services\BlockchainService\Dto\BlockchainConfig;
use Dkg\Services\BlockchainService\Dto\Asset;
use Dkg\Services\BlockchainService\Proxy\Web3ProxyInterface;
use Dkg\Services\BlockchainService\Proxy\Web3ProxyManager;
use Dkg\Services\BlockchainService\Proxy\Web3ProxyManagerInterface;
use Exception;
use Web3\Web3;

class BlockchainService implements BlockchainServiceInterface
{
    /** @var BlockchainConfig|null */
    private $config;

    /** @var Web3ProxyManagerInterface */
    private $web3ProxyManager;

    public function __construct(Web3ProxyManagerInterface $web3ProxyManager, ?BlockchainConfig $config)
    {
        $this->config = $config;
        $this->web3ProxyManager = $web3ProxyManager;

        if ($config) {
            $this->web3ProxyManager->initializeProxy($config->getBlockchainName());
        }
    }

    /**
     * @throws ConfigMissingException
     */
    public function createAsset(Asset $asset, float $tokenAmount, ?BlockchainConfig $config)
    {
        $mergedConfig = $this->getMergedConfig($config);
        $blockchainName = $mergedConfig->getBlockchainName();
        $proxy = $this->web3ProxyManager->getProxy($blockchainName);

        $proxy->increaseAllowance($tokenAmount, $mergedConfig->getPublicKey(), $mergedConfig->getPrivateKey());
    }

    /**
     * @throws ConfigMissingException
     */
    private function getMergedConfig(?BlockchainConfig $config): BlockchainConfig
    {
        $mergedConfig = new BlockchainConfig();

        if (!$config && !$this->config) {
            throw new ConfigMissingException("No blockchain is provided to BlockchainService.");
        }

        if ($this->config) {
            $mergedConfig->setBlockchainName($this->config->getBlockchainName());
            $mergedConfig->setPublicKey($this->config->getPublicKey());
            $mergedConfig->setPrivateKey($this->config->getPrivateKey());
        }

        if ($config) {
            if ($config->getBlockchainName()) {
                $mergedConfig->setBlockchainName($config->getBlockchainName());
            }

            if ($config->getPublicKey()) {
                $mergedConfig->setPublicKey($config->getPublicKey());
            }

            if ($config->getPrivateKey()) {
                $mergedConfig->setPrivateKey($config->getPrivateKey());
            }
        }

        return $mergedConfig;
    }

    /**
     * @param $blockchainName
     * @return bool
     */
    public static function isBlockchainSupported($blockchainName): bool
    {
        $supportedBlockchains = array_keys(Web3ProxyManager::getInstance()->getAvailableBlockchains());
        return in_array($blockchainName, $supportedBlockchains);
    }
}

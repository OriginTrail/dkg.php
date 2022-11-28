<?php

namespace Dkg\Services\BlockchainService;

use Dkg\Exceptions\ServiceMisconfigurationException;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\PublishOptions;
use Dkg\Services\BlockchainService\Dto\BlockchainConfig;
use Dkg\Services\BlockchainService\Services\Proxy\Web3ProxyManager;
use Dkg\Services\BlockchainService\Services\Proxy\Web3ProxyManagerInterface;

class BlockchainService implements BlockchainServiceInterface
{
    /** @var BlockchainConfig|null */
    private $baseConfig;

    /** @var Web3ProxyManagerInterface */
    private $web3ProxyManager;

    public function __construct(Web3ProxyManagerInterface $web3ProxyManager, ?BlockchainConfig $config)
    {
        $this->baseConfig = $config;
        $this->web3ProxyManager = $web3ProxyManager;
    }

    public function createAsset(Asset $asset, PublishOptions $options): Asset
    {
        $config = $this->getMergedConfig($options->getBlockchainConfig());
        $blockchainName = $config->getBlockchainName();
        $proxy = $this->web3ProxyManager->getProxy($blockchainName);

        $proxy->increaseAllowance($options->getTokenAmount(), $config);

        $createAssetArgs = [
            $asset->getAssertionId(),
            $asset->getAssertionSize(),
            $asset->getTriplesCount(),
            $options->getEpochsNum(),
            $asset->getChunkCount(),
            $options->getTokenAmount()
        ];

        [$contractAddress, $tokenId] = $proxy->createAsset($createAssetArgs, $config);

        $asset->setBlockchain($blockchainName);
        $asset->setContract($contractAddress);
        $asset->setTokenId((int)$tokenId->toString());

        return $asset;
    }

    /**
     * @throws ServiceMisconfigurationException
     */
    private function getMergedConfig(?BlockchainConfig $config): BlockchainConfig
    {
        $mergedConfig = new BlockchainConfig();

        if (!$config && !$this->baseConfig) {
            throw new ServiceMisconfigurationException("No blockchain is provided to BlockchainService.");
        }

        if ($this->baseConfig) {
            $mergedConfig->setBlockchainName($this->baseConfig->getBlockchainName());
            $mergedConfig->setPublicKey($this->baseConfig->getPublicKey());
            $mergedConfig->setPrivateKey($this->baseConfig->getPrivateKey());
            $mergedConfig->setNumOfRetries($this->baseConfig->getNumOfRetries());
            $mergedConfig->setPollFrequency($this->baseConfig->getPollFrequency());
        }

        if ($config) {
            if ($config->getBlockchainName()) {
                $mergedConfig->setBlockchainName($config->getBlockchainName());
            }

            if ($config->getPublicKey() && $config->getPrivateKey()) {
                $mergedConfig->setPublicKey($config->getPublicKey());
                $mergedConfig->setPrivateKey($config->getPrivateKey());
            }

            $mergedConfig->setNumOfRetries($config->getNumOfRetries());
            $mergedConfig->setPollFrequency($config->getPollFrequency());
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

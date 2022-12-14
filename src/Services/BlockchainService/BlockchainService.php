<?php

namespace Dkg\Services\BlockchainService;

use Dkg\Exceptions\BlockchainException;
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

        $proxy->increaseAllowance($options->getBidAmount(), $config);

        $createAssetArgs = [
            $asset->getAssertionId(),
            $asset->getAssertionSize(),
            $asset->getTriplesCount(),
            $asset->getChunkCount(),
            $options->getEpochsNum(),
            $options->getBidAmount()
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
    public function getLatestAssertionId(int $tokenId, BlockchainConfig $config): string
    {
        $config = $this->getMergedConfig($config);
        $blockchainName = $config->getBlockchainName();
        $proxy = $this->web3ProxyManager->getProxy($blockchainName);

        return $proxy->getLatestAssertionId($tokenId);
    }


    /**
     * @throws ServiceMisconfigurationException
     * @throws BlockchainException
     */
    public function updateAsset(Asset $asset, ?PublishOptions $options)
    {
        $config = $this->getMergedConfig($options->getBlockchainConfig());
        $blockchainName = $config->getBlockchainName();
        $proxy = $this->web3ProxyManager->getProxy($blockchainName);

        $proxy->increaseAllowance($options->getBidAmount(), $config);

        $updateAssetArgs = [
            $asset->getTokenId(),
            $asset->getAssertionId(),
            $asset->getAssertionSize(),
            $asset->getTriplesCount(),
            $asset->getChunkCount(),
            $options->getEpochsNum(),
            $options->getBidAmount()
        ];

        $proxy->updateAsset($updateAssetArgs, $config);
    }

    public function transferAsset(?string $tokenId, string $toAddress, ?BlockchainConfig $blockchainConfig)
    {
        $config = $this->getMergedConfig($blockchainConfig);
        $blockchainName = $config->getBlockchainName();
        $proxy = $this->web3ProxyManager->getProxy($blockchainName);

        $proxy->transferAsset($tokenId, $toAddress, $config);
    }

    /**
     * @throws ServiceMisconfigurationException
     */
    public function getAssetOwner(string $tokenId, ?BlockchainConfig $config = null): ?string
    {
        $config = $this->getMergedConfig($config);
        $blockchainName = $config->getBlockchainName();
        $proxy = $this->web3ProxyManager->getProxy($blockchainName);

        // todo enhance error handling
        return $proxy->getOwner($tokenId);
    }

    /**
     * @throws ServiceMisconfigurationException
     */
    public function getMergedConfig(?BlockchainConfig $config): BlockchainConfig
    {
        $mergedConfig = new BlockchainConfig();

        if (!$config && !$this->baseConfig) {
            throw new ServiceMisconfigurationException("No config is provided to BlockchainService.");
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

            if ($config->getNumOfRetries()) {
                $mergedConfig->setNumOfRetries($config->getNumOfRetries());
            }

            if ($config->getPollFrequency()) {
                $mergedConfig->setPollFrequency($config->getPollFrequency());
            }
        }

        if (!$mergedConfig->validate()) {
            throw new ServiceMisconfigurationException("Missing some non-default properties for BlockchainService");
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

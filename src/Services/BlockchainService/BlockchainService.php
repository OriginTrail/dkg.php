<?php

namespace Dkg\Services\BlockchainService;

use Dkg\Exceptions\ConfigMissingException;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\PublishOptions;
use Dkg\Services\BlockchainService\Dto\BlockchainConfig;
use Dkg\Services\BlockchainService\Services\Proxy\Web3ProxyManager;
use Dkg\Services\BlockchainService\Services\Proxy\Web3ProxyManagerInterface;

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
    }

    public function createAsset(Asset $asset, PublishOptions $options): Asset
    {
        $config = $this->getMergedConfig($options->getBlockchainConfig());
        $blockchainName = $config->getBlockchainName();
        $proxy = $this->web3ProxyManager->getProxy($blockchainName);

        $proxy->increaseAllowance($options->getTokenAmount(), $config->getPublicKey(), $config->getPrivateKey());

        $createAssetArgs = [
            $asset->getAssertionId(),
            $asset->getAssertionSize(),
            $asset->getTriplesCount(),
            $options->getEpochsNum(),
            $asset->getChunkCount(),
            $options->getTokenAmount()
        ];

        [$assetContract, $tokenId] = $proxy->createAsset($createAssetArgs, $config->getPublicKey(), $config->getPrivateKey());
        $contractAddress = $proxy->getContentAssetContractAddress();

        $asset->setBlockchain($blockchainName);
        $asset->setContract($contractAddress);
        $asset->setTokenId((int)$tokenId->toString());

        return $asset;
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

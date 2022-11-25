<?php

namespace Dkg;

use Dkg\Communication\Infrastructure\HttpClient\HttpClient;
use Dkg\Communication\NodeProxy;
use Dkg\Services\AssetService\AssetService;
use Dkg\Services\AssetService\AssetServiceInterface;
use Dkg\Services\BlockchainService\BlockchainService;
use Dkg\Services\BlockchainService\Proxy\Web3ProxyManager;
use Dkg\Services\GraphService\GraphServiceInterface;
use Dkg\Services\NodeService\NodeService;
use Dkg\Services\NodeService\NodeServiceInterface;

/**
 * Class for communication with DKG
 */
class Dkg implements DkgInterface
{
    /** @var NodeService */
    private $nodeService;

    /** @var AssetService */
    private $assetService;

    public function __construct(DkgConfig $config)
    {
        $nodeProxy = new NodeProxy(new HttpClient(), $config->getUrl());
        $web3ProxyManager = Web3ProxyManager::getInstance();
        $blockchainService = new BlockchainService($web3ProxyManager, $config->getBlockchainParams());

        $this->nodeService = new NodeService($nodeProxy);
        $this->assetService = new AssetService($nodeProxy, $blockchainService);
    }

    public function asset(): AssetServiceInterface
    {
        return $this->assetService;
    }

    public function node(): NodeServiceInterface
    {
        return $this->nodeService;
    }

    public function graph(): GraphServiceInterface
    {
        // TODO: Implement graph() method.
    }
}

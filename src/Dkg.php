<?php

namespace Dkg;

use Dkg\Communication\Infrastructure\HttpClient\HttpClient;
use Dkg\Communication\NodeProxy;
use Dkg\Config\DkgConfig;
use Dkg\Services\AssetService\AssetService;
use Dkg\Services\AssetService\AssetServiceInterface;
use Dkg\Services\BlockchainService\BlockchainService;
use Dkg\Services\BlockchainService\Services\Proxy\Web3ProxyManager;
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
        $nodeProxy = new NodeProxy(new HttpClient(), $config->getHttpConfig());
        $web3ProxyManager = Web3ProxyManager::getInstance();
        $blockchainService = new BlockchainService($web3ProxyManager, $config->getBlockchainConfig());

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

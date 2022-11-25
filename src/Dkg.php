<?php

namespace Dkg;

use Dkg\Communication\Infrastructure\HttpClient\HttpClient;
use Dkg\Communication\NodeProxy;
use Dkg\Communication\NodeProxyInterface;
use Dkg\Services\AssetService\AssetService;
use Dkg\Services\AssetService\AssetServiceInterface;
use Dkg\Services\GraphService\GraphServiceInterface;
use Dkg\Services\NodeService\NodeService;
use Dkg\Services\NodeService\NodeServiceInterface;

/**
 * Class for communication with DKG
 */
class Dkg implements DkgInterface
{
    /** @var NodeProxyInterface */
    private $nodeProxy;

    /** @var NodeService */
    private $nodeService;

    /** @var AssetService  */
    private $assetService;

    public function __construct(?string $baseUrl)
    {
        $this->nodeProxy = new NodeProxy(new HttpClient(), $baseUrl);
        $this->nodeService = new NodeService($this->nodeProxy);
        $this->assetService = new AssetService($this->nodeProxy);
    }

    public function graph(): GraphServiceInterface
    {
        // TODO: Implement graph() method.
    }

    public function asset(): AssetServiceInterface
    {
        return $this->assetService;
    }

    public function node(): NodeServiceInterface
    {
        return $this->nodeService;
    }
}

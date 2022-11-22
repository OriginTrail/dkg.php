<?php

namespace Dkg;

use Dkg\Communication\Infrastructure\HttpClient\HttpClient;
use Dkg\Communication\NodeProxy;
use Dkg\Communication\NodeProxyInterface;
use Dkg\Services\NodeService\NodeService;

/**
 * Class for communication with DKG
 */
class Dkg implements DkgInterface
{
    /** @var NodeProxyInterface */
    private $nodeProxy;

    /** @var NodeService  */
    private $nodeService;

    public function __construct(?string $baseUrl)
    {
        $this->nodeProxy = new NodeProxy(new HttpClient(), $baseUrl);
        $this->nodeService = new NodeService($this->nodeProxy);

    }

    public function graph()
    {
        // TODO: Implement graph() method.
    }

    public function asset()
    {
        // TODO: Implement asset() method.
    }

    public function node(): NodeService
    {
        return $this->nodeService;
    }
}

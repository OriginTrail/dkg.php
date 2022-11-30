<?php

namespace Dkg\Services\NodeService;


use Dkg\Communication\HttpClient\HttpResponse;
use Dkg\Communication\HttpConfig;
use Dkg\Communication\NodeProxyInterface;

class NodeService implements NodeServiceInterface
{
    /** @var NodeProxyInterface */
    private $nodeProxy;

    public function __construct(NodeProxyInterface $nodeProxy)
    {
        $this->nodeProxy = $nodeProxy;
    }

    public function getInfo(?HttpConfig $config = null): HttpResponse
    {
        return $this->nodeProxy->info($config);
    }
}

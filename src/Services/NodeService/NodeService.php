<?php

namespace Dkg\Services\NodeService;


use Dkg\Communication\Infrastructure\HttpClient\HttpMethods;
use Dkg\Communication\Infrastructure\HttpClient\HttpResponse;
use Dkg\Communication\NodeProxyInterface;

class NodeService
{
    /** @var NodeProxyInterface */
    private $nodeProxy;

    public function __construct(NodeProxyInterface $nodeProxy)
    {
        $this->nodeProxy = $nodeProxy;
    }


    public function getInfo(): HttpResponse
    {
        return $this->nodeProxy->sendRequest(HttpMethods::GET_METHOD, '/info');
    }
}

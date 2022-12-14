<?php

namespace Dkg\Services\GraphService;

use Dkg\Communication\HttpConfig;
use Dkg\Communication\NodeProxyInterface;
use Dkg\Communication\OperationResult;
use Dkg\Services\Params;

class GraphService implements GraphServiceInterface
{
    /** @var NodeProxyInterface */
    private $nodeProxy;

    public function __construct(NodeProxyInterface $nodeProxy)
    {
        $this->nodeProxy = $nodeProxy;
    }

    public function query(string $query, ?string $queryType = Params::QUERY_TYPE_SELECT, ?HttpConfig $config = null): OperationResult
    {
        if ($config) {
            $config = HttpConfig::default();
        }

        $response = $this->nodeProxy->query($query, $queryType, $config);

        if(!$response->isSuccess()) {
            return $response;
        }

        $jsonData = json_decode(json_encode($response->getData()), true);
        $response->setData($jsonData);

        return $response;
    }
}

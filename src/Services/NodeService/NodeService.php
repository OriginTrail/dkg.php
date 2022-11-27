<?php

namespace Dkg\Services\NodeService;


use Dkg\Communication\HttpConfig;
use Dkg\Communication\Infrastructure\Exceptions\CommunicationException;
use Dkg\Communication\Infrastructure\HttpClient\HttpResponse;
use Dkg\Communication\NodeProxyInterface;
use Dkg\Exceptions\ConfigMissingException;
use Dkg\Services\RequestOptions;

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
        if ($config) {
            return $this->nodeProxy->info(
                $config->getBaseUrl(),
                $config->getAuthToken()
            );
        }

        return $this->nodeProxy->info();
    }
}

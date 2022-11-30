<?php

namespace Dkg\Services\NodeService;

use Dkg\Communication\Exceptions\NodeProxyException;
use Dkg\Communication\HttpClient\HttpResponse;
use Dkg\Communication\HttpConfig;
use Dkg\Exceptions\ServiceMisconfigurationException;

interface NodeServiceInterface
{
    /**
     * Returns OT Node information.
     * @param HttpConfig|null $config
     * @return HttpResponse
     * @throws ServiceMisconfigurationException
     * @throws NodeProxyException
     */
    public function getInfo(?HttpConfig $config): HttpResponse;
}

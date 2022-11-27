<?php

namespace Dkg\Services\NodeService;

use Dkg\Communication\HttpConfig;
use Dkg\Communication\Infrastructure\Exceptions\CommunicationException;
use Dkg\Communication\Infrastructure\HttpClient\HttpResponse;
use Dkg\Exceptions\ConfigMissingException;

interface NodeServiceInterface
{
    /**
     * Returns OT Node information.
     * @param HttpConfig|null $config
     * @return HttpResponse
     * @throws ConfigMissingException
     * @throws CommunicationException
     */
    public function getInfo(?HttpConfig $config): HttpResponse;
}

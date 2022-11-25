<?php

namespace Dkg\Services\NodeService;

use Dkg\Communication\Infrastructure\HttpClient\HttpResponse;

interface NodeServiceInterface
{
    /**
     * Returns OT Node information.
     * @return HttpResponse
     */
    public function getInfo(): HttpResponse;
}

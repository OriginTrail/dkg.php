<?php

namespace Dkg\Services\GraphService;

use Dkg\Communication\HttpConfig;
use Dkg\Communication\OperationResult;
use Dkg\Services\Params;

interface GraphServiceInterface
{
    public function query(string $query, ?string $queryType=Params::QUERY_TYPE_SELECT, HttpConfig $config = null): OperationResult;
}

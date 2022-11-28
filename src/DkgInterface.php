<?php

namespace Dkg;

use Dkg\Services\AssetService\AssetServiceInterface;
use Dkg\Services\GraphService\GraphServiceInterface;
use Dkg\Services\NodeService\NodeServiceInterface;

interface DkgInterface
{
    /**
     * Returns node interface
     */
    public function node(): NodeServiceInterface;

    public function asset(): AssetServiceInterface;

    public function graph(): GraphServiceInterface;
}

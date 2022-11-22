<?php

namespace Dkg;

use Dkg\Services\NodeService\NodeService;

interface DkgInterface
{
    /**
     * Returns node interface
     */
    public function node(): NodeService;
    public function asset();
    public function graph();
}

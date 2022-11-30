<?php

namespace Dkg\Services;

use Dkg\Communication\HttpConfig;
use Dkg\Services\BlockchainService\Dto\BlockchainConfig;

class RequestOptions
{
    /** @var HttpConfig|null */
    protected $httpConfig;

    /** @var BlockchainConfig|null */
    protected $blockchainConfig;

    /**
     * @return HttpConfig|null
     */
    public function getHttpConfig(): ?HttpConfig
    {
        return $this->httpConfig;
    }

    /**
     * @param HttpConfig|null $httpConfig
     */
    public function setHttpConfig(?HttpConfig $httpConfig): void
    {
        $this->httpConfig = $httpConfig;
    }

    /**
     * @return BlockchainConfig|null
     */
    public function getBlockchainConfig(): ?BlockchainConfig
    {
        return $this->blockchainConfig;
    }

    /**
     * @param BlockchainConfig $blockchainConfig
     */
    public function setBlockchainConfig(BlockchainConfig $blockchainConfig): void
    {
        $this->blockchainConfig = $blockchainConfig;
    }
}

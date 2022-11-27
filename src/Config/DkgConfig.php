<?php

namespace Dkg\Config;

use Dkg\Communication\HttpConfig;
use Dkg\Services\BlockchainService\Dto\BlockchainConfig;

class DkgConfig
{
    /** @var HttpConfig|null */
    private $httpConfig;

    /** @var BlockchainConfig|null */
    private $blockchainConfig;

    public function __construct()
    {
        $this->httpConfig = new HttpConfig();
        $this->blockchainConfig = new BlockchainConfig();
    }

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
     * @param BlockchainConfig|null $blockchainConfig
     */
    public function setBlockchainConfig(?BlockchainConfig $blockchainConfig): void
    {
        $this->blockchainConfig = $blockchainConfig;
    }
}

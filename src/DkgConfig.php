<?php

namespace Dkg;

use Dkg\Services\BlockchainService\Dto\BlockchainConfig;

class DkgConfig
{
    /** @var string */
    private $url;

    /** @var BlockchainConfig|null */
    private $blockchainParams;

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return BlockchainConfig|null
     */
    public function getBlockchainParams(): ?BlockchainConfig
    {
        return $this->blockchainParams;
    }

    /**
     * @param BlockchainConfig|null $blockchainParams
     */
    public function setBlockchainParams(?BlockchainConfig $blockchainParams): void
    {
        $this->blockchainParams = $blockchainParams;
    }
}

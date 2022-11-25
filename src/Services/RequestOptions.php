<?php

namespace Dkg\Services;

use Dkg\Services\BlockchainService\Dto\BlockchainConfig;

class RequestOptions
{
    protected $maxNumOfRetries = Constants::HTTP_DEFAULT_MAX_NUM_OF_RETRIES;
    protected $retryFrequency = Constants::HTTP_DEFAULT_FREQUENCY_IN_SEC;

    /** @var string|null */
    protected $url;

    /** @var BlockchainConfig|null */
    protected $blockchainConfig;

    /**
     * @return int
     */
    public function getMaxNumOfRetries(): int
    {
        return $this->maxNumOfRetries;
    }

    /**
     * @param int $maxNumOfRetries
     */
    public function setMaxNumOfRetries(int $maxNumOfRetries): void
    {
        $this->maxNumOfRetries = $maxNumOfRetries;
    }

    /**
     * @return int
     */
    public function getRetryFrequency(): int
    {
        return $this->retryFrequency;
    }

    /**
     * @param int $retryFrequency
     */
    public function setRetryFrequency(int $retryFrequency): void
    {
        $this->retryFrequency = $retryFrequency;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
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

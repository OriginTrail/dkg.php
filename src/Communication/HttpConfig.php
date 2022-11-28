<?php

namespace Dkg\Communication;

use Dkg\Config\Constants;

class HttpConfig
{
    /** @var int */
    private $maxNumOfRetries = Constants::HTTP_DEFAULT_MAX_NUM_OF_RETRIES;

    /** @var int */
    private $retryFrequency = Constants::HTTP_DEFAULT_POLL_FREQUENCY_IN_MS;

    /** @var string|null */
    private $baseUrl;

    /** @var string|null */
    private $authToken;

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
    public function getRetryFrequency()
    {
        return $this->retryFrequency;
    }

    /**
     * @param int $retryFrequency retry frequency in ms
     */
    public function setRetryFrequency($retryFrequency): void
    {
        $this->retryFrequency = $retryFrequency;
    }

    /**
     * @return string|null
     */
    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    /**
     * @param string|null $baseUrl
     */
    public function setBaseUrl(?string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return string|null
     */
    public function getAuthToken(): ?string
    {
        return $this->authToken;
    }

    /**
     * @param string|null $authToken
     */
    public function setAuthToken(?string $authToken): void
    {
        $this->authToken = $authToken;
    }
}

<?php

namespace Dkg\Communication;

use Dkg\Services\Params;

class HttpConfig
{
    /** @var int|null */
    private $maxNumOfRetries;

    /** @var int|null */
    private $retryFrequency;

    /** @var string|null */
    private $baseUrl;

    /** @var string|null */
    private $authToken;

    /**
     * @return int|null
     */
    public function getMaxNumOfRetries(): ?int
    {
        return $this->maxNumOfRetries;
    }

    /**
     * @param int|null $maxNumOfRetries
     */
    public function setMaxNumOfRetries(?int $maxNumOfRetries): void
    {
        $this->maxNumOfRetries = $maxNumOfRetries;
    }

    /**
     * @return int|null
     */
    public function getRetryFrequency(): ?int
    {
        return $this->retryFrequency;
    }

    /**
     * @param int|null $retryFrequency retry frequency in ms
     */
    public function setRetryFrequency(?int $retryFrequency): void
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

    /**
     * Validate whether mandatory fields are present.
     * @return bool
     */
    public function validate(): bool
    {
        return
            $this->maxNumOfRetries &&
            $this->retryFrequency &&
            $this->baseUrl;
    }

    public static function default(): HttpConfig
    {
        $config = new HttpConfig();
        $config->setMaxNumOfRetries(Params::HTTP_DEFAULT_MAX_NUM_OF_RETRIES);
        $config->setRetryFrequency(Params::HTTP_DEFAULT_POLL_FREQUENCY_IN_MS);

        return $config;
    }
}

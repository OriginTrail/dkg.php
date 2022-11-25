<?php

namespace Dkg\Services\AssetService\Dto;

class PublishOptions
{
    /** @var string|null */
    private $visibility;

    /** @var bool|null */
    private $localStore;

    /** @var int|null */
    private $holdingTimeInYears;

    /** @var float|null */
    private $tokenAmount;

    /** @var int|null */
    private $maxNumOfRetries;

    /** @var string|null */
    private $blockchain;

    /**
     * @return string|null
     */
    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility 'public' | 'private'
     */
    public function setVisibility(string $visibility): void
    {
        $this->visibility = $visibility;
    }

    /**
     * @return bool|null
     */
    public function isLocalStore(): ?bool
    {
        return $this->localStore;
    }

    /**
     * @param bool $localStore
     */
    public function setLocalStore(bool $localStore): void
    {
        $this->localStore = $localStore;
    }

    /**
     * @return int
     */
    public function getHoldingTimeInYears(): int
    {
        return $this->holdingTimeInYears;
    }

    /**
     * @param int $holdingTimeInYears
     */
    public function setHoldingTimeInYears(int $holdingTimeInYears): void
    {
        $this->holdingTimeInYears = $holdingTimeInYears;
    }

    /**
     * @return float|null
     */
    public function getTokenAmount(): ?float
    {
        return $this->tokenAmount;
    }

    /**
     * @param float $tokenAmount
     */
    public function setTokenAmount(float $tokenAmount): void
    {
        $this->tokenAmount = $tokenAmount;
    }

    /**
     * @return int|null
     */
    public function getMaxNumOfRetries(): ?int
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
     * @return string|null
     */
    public function getBlockchain(): ?string
    {
        return $this->blockchain;
    }

    /**
     * @param string $blockchain
     */
    public function setBlockchain(string $blockchain): void
    {
        $this->blockchain = $blockchain;
    }
}

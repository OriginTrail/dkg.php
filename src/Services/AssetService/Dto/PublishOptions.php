<?php

namespace Dkg\Services\AssetService\Dto;

use Dkg\Services\Constants;
use Dkg\Services\RequestOptions;

class PublishOptions extends RequestOptions
{
    /** @var string|null */
    private $visibility = Constants::VISIBILITY_PUBLIC;

    /** @var bool */
    private $localStore = false;

    /** @var int */
    private $epochsNum = Constants::PUBLISH_DEFAULT_EPOCH_NUM;

    /** @var float */
    private $tokenAmount = Constants::PUBLISH_DEFAULT_TOKEN_AMOUNT;

    /**
     * @return string
     */
    public function getVisibility(): string
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
     * @return bool
     */
    public function isLocalStore(): bool
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
    public function getEpochsNum(): int
    {
        return $this->epochsNum;
    }

    /**
     * @param int $epochsNum
     */
    public function setEpochsNum(int $epochsNum): void
    {
        $this->epochsNum = $epochsNum;
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
}

<?php

namespace Dkg\Services\AssetService\Dto;

use Dkg\Config\Constants;
use Dkg\Services\RequestOptions;

class PublishOptions extends RequestOptions
{
    private $publishType = Constants::PUBLISH_TYPE_ASSET;

    /** @var bool */
    private $localStore = false;

    /** @var int */
    private $epochsNum = Constants::PUBLISH_DEFAULT_EPOCH_NUM;

    /** @var float */
    private $tokenAmount = Constants::PUBLISH_DEFAULT_TOKEN_AMOUNT;

    /** @var int */
    private $hashFunctionId = Constants::PUBLISH_DEFAULT_HASH_FUNCTION_ID;

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

    /**
     * @return int
     */
    public function getHashFunctionId(): int
    {
        return $this->hashFunctionId;
    }

    /**
     * @return string
     */
    public function getPublishType(): string
    {
        return $this->publishType;
    }
}

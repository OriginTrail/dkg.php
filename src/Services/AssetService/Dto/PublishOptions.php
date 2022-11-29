<?php

namespace Dkg\Services\AssetService\Dto;

use Dkg\Communication\HttpConfig;
use Dkg\Services\BlockchainService\Dto\BlockchainConfig;
use Dkg\Services\Constants;
use Dkg\Services\RequestOptions;

class PublishOptions extends RequestOptions
{
    /** @var string|null */
    private $publishType;

    /** @var bool|null */
    private $localStore;

    /** @var int|null */
    private $epochsNum;

    /** @var float|null */
    private $tokenAmount;

    /** @var int|null */
    private $hashFunctionId;

    public function __construct()
    {
        $this->httpConfig = new HttpConfig();
        $this->blockchainConfig = new BlockchainConfig();
    }

    /**
     * @return string|null
     */
    public function getPublishType(): ?string
    {
        return $this->publishType;
    }

    /**
     * @param string|null $publishType
     */
    public function setPublishType(?string $publishType): void
    {
        $this->publishType = $publishType;
    }

    /**
     * @return bool|null
     */
    public function isLocalStore(): ?bool
    {
        return $this->localStore;
    }

    /**
     * @param bool|null $localStore
     */
    public function setLocalStore(?bool $localStore): void
    {
        $this->localStore = $localStore;
    }

    /**
     * @return int|null
     */
    public function getEpochsNum(): ?int
    {
        return $this->epochsNum;
    }

    /**
     * @param int|null $epochsNum
     */
    public function setEpochsNum(?int $epochsNum): void
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
     * @param float|null $tokenAmount
     */
    public function setTokenAmount(?float $tokenAmount): void
    {
        $this->tokenAmount = $tokenAmount;
    }

    /**
     * @return int|null
     */
    public function getHashFunctionId(): ?int
    {
        return $this->hashFunctionId;
    }

    /**
     * @param int|null $hashFunctionId
     */
    public function setHashFunctionId(?int $hashFunctionId): void
    {
        $this->hashFunctionId = $hashFunctionId;
    }

    public function validate(): bool
    {
        return
            $this->publishType &&
            isset($this->localStore) &&
            $this->epochsNum &&
            $this->tokenAmount &&
            isset($this->hashFunctionId);
    }

    public static function default(): PublishOptions
    {
        $options = new PublishOptions();
        $options->setPublishType(Constants::PUBLISH_TYPE_ASSET);
        $options->setLocalStore(false);
        $options->setEpochsNum(Constants::PUBLISH_DEFAULT_EPOCH_NUM);
        $options->setTokenAmount(Constants::PUBLISH_DEFAULT_TOKEN_AMOUNT);
        $options->setHashFunctionId(Constants::DEFAULT_HASH_FUNCTION_ID);
        $options->setBlockchainConfig(new BlockchainConfig());
        $options->setHttpConfig(new HttpConfig());

        return $options;
    }
}

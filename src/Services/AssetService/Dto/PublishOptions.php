<?php

namespace Dkg\Services\AssetService\Dto;

use Dkg\Communication\HttpConfig;
use Dkg\Services\BlockchainService\Dto\BlockchainConfig;
use Dkg\Services\Params;
use Dkg\Services\RequestOptions;

class PublishOptions extends RequestOptions
{
    /** @var string|null */
    private $publishType;

    /** @var bool|null */
    private $localStore;

    /** @var int|null */
    private $epochsNum;

    /**
     * Token amount in ether.
     * If token amount is not set, bid suggestion will be taken
     * from the network.
     * @var float|null Token amount in ether.
     */
    private $tokenAmount;

    /** @var int|null */
    private $hashFunctionId;

    /** @var int|null */
    private $scoreFunctionId;

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
     * Returns token amount in ether.
     * @return float|null
     */
    public function getTokenAmount(): ?float
    {
        return $this->tokenAmount;
    }

    /**
     * Sets token amount in ether.
     * If token amount is not set, bid suggestion will be taken
     * from the network.
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

    /**
     * @return int|null
     */
    public function getScoreFunctionId(): ?int
    {
        return $this->scoreFunctionId;
    }

    /**
     * @param int|null $scoreFunctionId
     */
    public function setScoreFunctionId(?int $scoreFunctionId): void
    {
        $this->scoreFunctionId = $scoreFunctionId;
    }

    /**
     * Validate whether mandatory fields are present.
     * @return bool
     */
    public function validate(): bool
    {
        return
            $this->publishType &&
            isset($this->localStore) &&
            $this->epochsNum &&
            isset($this->hashFunctionId);
    }

    public static function default(): PublishOptions
    {
        $options = new PublishOptions();
        $options->setPublishType(Params::PUBLISH_TYPE_ASSET);
        $options->setLocalStore(false);
        $options->setEpochsNum(Params::PUBLISH_DEFAULT_EPOCH_NUM);
        $options->setHashFunctionId(Params::DEFAULT_HASH_FUNCTION_ID);
        $options->setScoreFunctionId(Params::DEFAULT_SCORE_FUNCTION_ID);
        $options->setBlockchainConfig(new BlockchainConfig());
        $options->setHttpConfig(new HttpConfig());

        return $options;
    }
}

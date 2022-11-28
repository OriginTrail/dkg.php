<?php

namespace Dkg\Services\BlockchainService\Dto;

use Dkg\Config\Constants;

/**
 * This class is used as an argument for blockchain initialization.
 */
class BlockchainConfig
{
    /** @var string|null */
    private $blockchainName;

    /** @var string|null */
    private $privateKey;

    /** @var  string|null */
    private $publicKey;

    /** @var int|null Used for polling tx receipt */
    private $numOfRetries;

    /** @var int|null Used for polling tx receipt */
    private $pollFrequency;

    /**
     * @return string|null
     */
    public function getBlockchainName(): ?string
    {
        return $this->blockchainName;
    }

    /**
     * @param string|null $blockchainName
     */
    public function setBlockchainName(?string $blockchainName): void
    {
        $this->blockchainName = $blockchainName;
    }

    /**
     * @return string|null
     */
    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    /**
     * @param string|null $privateKey
     */
    public function setPrivateKey(?string $privateKey): void
    {
        $this->privateKey = $privateKey;
    }

    /**
     * @return string|null
     */
    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * @param string|null $publicKey
     */
    public function setPublicKey(?string $publicKey): void
    {
        $this->publicKey = $publicKey;
    }

    /**
     * @return int|null
     */
    public function getNumOfRetries(): ?int
    {
        return $this->numOfRetries;
    }

    /**
     * @param int|null $numOfRetries
     */
    public function setNumOfRetries(?int $numOfRetries): void
    {
        $this->numOfRetries = $numOfRetries;
    }

    /**
     * Gets polling frequency in ms
     * @return int|null
     */
    public function getPollFrequency(): ?int
    {
        return $this->pollFrequency;
    }

    /**
     * Sets polling frequency in ms
     * @param int|null $pollFrequency
     */
    public function setPollFrequency(?int $pollFrequency): void
    {
        $this->pollFrequency = $pollFrequency;
    }

    /**
     * Returns bool indicator whether config is validated
     * @return bool
     */
    public function validate(): bool
    {
        return
            $this->blockchainName &&
            $this->privateKey &&
            $this->publicKey &&
            $this->numOfRetries &&
            $this->pollFrequency;
    }

    public static function default(): BlockchainConfig
    {
        $config = new BlockchainConfig();
        $config->setNumOfRetries(Constants::BLOCKCHAIN_DEFAULT_NUM_OF_RETRIES);
        $config->setPollFrequency(Constants::BLOCKCHAIN_DEFAULT_POLL_FREQUENCY_IN_MS);

        return $config;
    }
}

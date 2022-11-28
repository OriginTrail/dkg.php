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

    /** @var int Used for polling tx receipt */
    private $numOfRetries = Constants::BLOCKCHAIN_DEFAULT_NUM_OF_RETRIES;

    /** @var int Used for polling tx receipt */
    private $pollFrequency = Constants::BLOCKCHAIN_DEFAULT_POLL_FREQUENCY_IN_MS;

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
     * @return int
     */
    public function getNumOfRetries(): int
    {
        return $this->numOfRetries;
    }

    /**
     * @param int $numOfRetries
     */
    public function setNumOfRetries(int $numOfRetries): void
    {
        $this->numOfRetries = $numOfRetries;
    }

    /**
     * @return int
     */
    public function getPollFrequency(): int
    {
        return $this->pollFrequency;
    }

    /**
     * @param int $pollFrequency
     */
    public function setPollFrequency(int $pollFrequency): void
    {
        $this->pollFrequency = $pollFrequency;
    }
}

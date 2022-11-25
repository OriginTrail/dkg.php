<?php

namespace Dkg\Services\BlockchainService\Dto;

/**
 * This class is used as an argument for blockchain initialization.
 */
class BlockchainConfig
{
    /** @var string|null */
    private $blockchainName;

    /** @var string|null */
    private $privateKey;

    /** @var string|null */
    private $publicKey;

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
}

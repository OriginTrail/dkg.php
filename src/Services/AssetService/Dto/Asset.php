<?php

namespace Dkg\Services\AssetService\Dto;

class Asset
{
    /** @var array|null */
    private $assertion;

    /** @var string */
    private $assertionId;

    /** @var int */
    private $assertionSize;

    /** @var int */
    private $triplesCount;

    /** @var int */
    private $chunkCount;

    /** @var string|null */
    private $blockchain;

    /** @var string|null */
    private $contract;

    /** @var int|null */
    private $tokenId;

    /** @var string|null */
    private $uai;

    /**
     * @return array|null
     */
    public function getAssertion(): ?array
    {
        return $this->assertion;
    }

    /**
     * @param array|null $assertion
     */
    public function setAssertion(?array $assertion): void
    {
        $this->assertion = $assertion;
    }

    /**
     * @return string
     */
    public function getAssertionId(): string
    {
        return $this->assertionId;
    }

    /**
     * @param string $assertionId
     */
    public function setAssertionId(string $assertionId): void
    {
        $this->assertionId = $assertionId;
    }

    /**
     * @return int
     */
    public function getAssertionSize(): int
    {
        return $this->assertionSize;
    }

    /**
     * @param int $assertionSize
     */
    public function setAssertionSize(int $assertionSize): void
    {
        $this->assertionSize = $assertionSize;
    }

    /**
     * @return int
     */
    public function getTriplesCount(): int
    {
        return $this->triplesCount;
    }

    /**
     * @param int $triplesCount
     */
    public function setTriplesCount(int $triplesCount): void
    {
        $this->triplesCount = $triplesCount;
    }

    /**
     * @return int
     */
    public function getChunkCount(): int
    {
        return $this->chunkCount;
    }

    /**
     * @param int $chunkCount
     */
    public function setChunkCount(int $chunkCount): void
    {
        $this->chunkCount = $chunkCount;
    }

    /**
     * @return string|null
     */
    public function getBlockchain(): ?string
    {
        return $this->blockchain;
    }

    /**
     * @param string|null $blockchain
     */
    public function setBlockchain(?string $blockchain): void
    {
        $this->blockchain = $blockchain;
    }

    /**
     * @return string|null
     */
    public function getContract(): ?string
    {
        return $this->contract;
    }

    /**
     * @param string|null $contract
     */
    public function setContract(?string $contract): void
    {
        $this->contract = $contract;
    }

    /**
     * @return int|null
     */
    public function getTokenId(): ?int
    {
        return $this->tokenId;
    }

    /**
     * @param int|null $tokenId
     */
    public function setTokenId(?int $tokenId): void
    {
        $this->tokenId = $tokenId;
    }

    /**
     * @return ?string
     */
    public function getUai(): ?string
    {
        return $this->uai;
    }

    /**
     * @param string $uai
     */
    public function setUai(string $uai): void
    {
        $this->uai = $uai;
    }
}

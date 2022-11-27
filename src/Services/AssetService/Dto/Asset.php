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

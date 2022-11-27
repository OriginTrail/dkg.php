<?php

namespace Dkg\Services\BlockchainService\Dto;

class Asset
{
    /** @var string */
    private $assertionId;

    /** @var int */
    private $assertionSize;

    /** @var int */
    private $triplesCount;

    /** @var int */
    private $chunkCount;

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
}

<?php

namespace Dkg\Services\BlockchainService\Dto;

class Asset
{
    /** @var string */
    private $assertionId;

    /** @var float */
    private $assertionSize;

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
     * @return float
     */
    public function getAssertionSize(): float
    {
        return $this->assertionSize;
    }

    /**
     * @param float $assertionSize
     */
    public function setAssertionSize(float $assertionSize): void
    {
        $this->assertionSize = $assertionSize;
    }
}

<?php

namespace Dkg\Services\AssetService\Dto;

use Dkg\Communication\OperationResult;

class TransferResult
{
    /** @var string */
    private $uai;

    /** @var string */
    private $owner;

    /** @var OperationResult|null */
    private $operationResult;

    /**
     * @return string
     */
    public function getUai(): string
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

    /**
     * @return string
     */
    public function getOwner(): string
    {
        return $this->owner;
    }

    /**
     * @param string $owner
     */
    public function setOwner(string $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return OperationResult|null
     */
    public function getOperationResult(): ?OperationResult
    {
        return $this->operationResult;
    }

    /**
     * @param OperationResult|null $operationResult
     */
    public function setOperationResult(?OperationResult $operationResult): void
    {
        $this->operationResult = $operationResult;
    }
}

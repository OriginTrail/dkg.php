<?php

namespace Dkg\Services\AssetService\Dto;

use Dkg\Communication\OperationResult;

class PublishResult
{
    /** @var Asset|null */
    private $asset;

    /** @var OperationResult|null */
    private $operationResult;

    /**
     * @return Asset|null
     */
    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    /**
     * @param Asset|null $asset
     */
    public function setAsset(?Asset $asset): void
    {
        $this->asset = $asset;
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

<?php

namespace Dkg\Services\AssetService\Dto;

use Dkg\Communication\OperationResult;

class GetResult
{
    /** @var array|null */
    private $assertion;

    /** @var string|null */
    private $assertionId;

    /** @var OperationResult|null */
    private $nodeResponse;

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
     * @return string|null
     */
    public function getAssertionId(): ?string
    {
        return $this->assertionId;
    }

    /**
     * @param string|null $assertionId
     */
    public function setAssertionId(?string $assertionId): void
    {
        $this->assertionId = $assertionId;
    }

    /**
     * @return OperationResult|null
     */
    public function getNodeResponse(): ?OperationResult
    {
        return $this->nodeResponse;
    }

    /**
     * @param OperationResult|null $nodeResponse
     */
    public function setNodeResponse(?OperationResult $nodeResponse): void
    {
        $this->nodeResponse = $nodeResponse;
    }
}

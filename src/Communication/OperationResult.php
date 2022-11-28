<?php

namespace Dkg\Communication;

class OperationResult
{
    /** @var string */
    private $status;

    /** @var object|null */
    private $data;

    /** @var string|null */
    private $operationId;

    /**
     * @param string $status
     * @param object|null $data
     * @param string|null $operationId
     */
    public function __construct(string $status, ?object $data, ?string $operationId)
    {
        $this->status = $status;
        $this->data = $data;
        $this->operationId = $operationId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return object|null
     */
    public function getData(): ?object
    {
        return $this->data;
    }

    /**
     * @param object|null $data
     */
    public function setData(?object $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string|null
     */
    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    /**
     * @param string|null $operationId
     */
    public function setOperationId(?string $operationId): void
    {
        $this->operationId = $operationId;
    }
}

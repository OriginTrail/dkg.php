<?php

namespace Dkg\Communication;

class OperationResult
{
    /** @var string */
    private $status;

    /** @var object|array|null */
    private $data;

    /** @var string|null */
    private $operationId;

    /**
     * @param string $status
     * @param object|array|null $data
     * @param string|null $operationId
     */
    public function __construct(string $status, $data = null, ?string $operationId = null)
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
     * @return array|object|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array|object|null $data
     */
    public function setData($data): void
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

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->status === 'COMPLETED';
    }
}

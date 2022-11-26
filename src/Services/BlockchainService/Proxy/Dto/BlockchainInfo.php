<?php

namespace Dkg\Services\BlockchainService\Proxy\Dto;

/**
 * The class is used to hold Blockchain configuration
 */
class BlockchainInfo
{
    /** @var string */
    private $name;

    /** @var string */
    private $rpc;

    /** @var string */
    private $hubContract;

    /** @var int */
    private $chainId;

    /**
     * @param string $name
     * @param string $rpc
     * @param string $hubContract
     * @param $chainId
     */
    public function __construct(string $name, string $rpc, string $hubContract, $chainId)
    {
        $this->name = $name;
        $this->rpc = $rpc;
        $this->hubContract = $hubContract;
        $this->chainId = $chainId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRpc(): string
    {
        return $this->rpc;
    }

    /**
     * @param string $rpc
     */
    public function setRpc(string $rpc): void
    {
        $this->rpc = $rpc;
    }

    /**
     * @return string
     */
    public function getHubContract(): string
    {
        return $this->hubContract;
    }

    /**
     * @param string $hubContract
     */
    public function setHubContract(string $hubContract): void
    {
        $this->hubContract = $hubContract;
    }

    /**
     * @return int
     */
    public function getChainId(): int
    {
        return $this->chainId;
    }

    /**
     * @param int $chainId
     */
    public function setChainId(int $chainId): void
    {
        $this->chainId = $chainId;
    }
}


<?php

namespace Dkg\Services\BlockchainService\Dto;

class BlockchainConfig {
    /** @var string */
    private $rpc;

    /** @var string */
    private $hubContract;

    /**
     * @param string $rpc
     * @param string $hubContract
     */
    public function __construct(string $rpc, string $hubContract)
    {
        $this->rpc = $rpc;
        $this->hubContract = $hubContract;
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
}


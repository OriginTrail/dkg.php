<?php

namespace Dkg\Services\BlockchainService\Services\AbiManager\Dto;

class AbiEvent
{
    /** @var string[] */
    private $inputTypes;

    /** @var string */
    private $hash;

    /**
     * @return string[]
     */
    public function getInputTypes(): array
    {
        return $this->inputTypes;
    }

    /**
     * @param string[] $inputTypes
     */
    public function setInputTypes(array $inputTypes): void
    {
        $this->inputTypes = $inputTypes;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }
}

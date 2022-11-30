<?php

namespace Dkg\Services\AssetService\Dto;

use Dkg\Communication\HttpConfig;
use Dkg\Services\BlockchainService\Dto\BlockchainConfig;
use Dkg\Services\Constants;
use Dkg\Services\RequestOptions;

class GetOptions extends RequestOptions
{
    /** @var bool|null */
    private $validate;

    /** @var int|null */
    private $hashFunctionId;

    /** @var string|null */
    private $outputFormat;

    public function __construct()
    {
        $this->httpConfig = new HttpConfig();
        $this->blockchainConfig = new BlockchainConfig();
    }

    /**
     * @return bool|null
     */
    public function getValidate(): ?bool
    {
        return $this->validate;
    }

    /**
     * @param bool|null $validate
     */
    public function setValidate(?bool $validate): void
    {
        $this->validate = $validate;
    }

    /**
     * @return int|null
     */
    public function getHashFunctionId(): ?int
    {
        return $this->hashFunctionId;
    }

    /**
     * @param int|null $hashFunctionId
     */
    public function setHashFunctionId(?int $hashFunctionId): void
    {
        $this->hashFunctionId = $hashFunctionId;
    }

    /**
     * @return string|null
     */
    public function getOutputFormat(): ?string
    {
        return $this->outputFormat;
    }

    /**
     * @param string|null $outputFormat
     */
    public function setOutputFormat(?string $outputFormat): void
    {
        $this->outputFormat = $outputFormat;
    }

    public static function default(): GetOptions
    {
        $options = new GetOptions();
        $options->setValidate(Constants::GET_DEFAULT_VALIDATE);
        $options->setHashFunctionId(Constants::DEFAULT_HASH_FUNCTION_ID);
        $options->setOutputFormat(Constants::JSONLD_FORMAT_NQUADS);

        return $options;
    }
}

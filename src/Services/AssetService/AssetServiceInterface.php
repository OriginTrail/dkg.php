<?php

namespace Dkg\Services\AssetService;

use Dkg\Communication\OperationResult;
use Dkg\Services\AssetService\Dto\GetOptions;
use Dkg\Services\AssetService\Dto\GetResult;
use Dkg\Services\AssetService\Dto\PublishOptions;
use Dkg\Services\AssetService\Dto\PublishResult;
use Dkg\Services\AssetService\Dto\TransferResult;
use Dkg\Services\BlockchainService\Dto\BlockchainConfig;

interface AssetServiceInterface
{
    /**
     * @param array $content
     * @param PublishOptions|null $options
     * @param array $stepHooks
     * @return PublishResult
     */
    public function create(array $content, ?PublishOptions $options = null, array $stepHooks = []): PublishResult;

    /**
     * @param string $uai
     * @param GetOptions|null $options
     * @return GetResult
     */
    public function get(string $uai, ?GetOptions $options = null): GetResult;

    /**
     * @param string $uai
     * @param array $content
     * @param PublishOptions|null $options
     * @return PublishResult
     */
    public function update(string $uai, array $content, ?PublishOptions $options = null): PublishResult;

    /**
     * @param string $uai
     * @param string $toAddress
     * @param BlockchainConfig|null $blockchainConfig
     * @return TransferResult
     */
    public function transfer(string $uai, string $toAddress, ?BlockchainConfig $blockchainConfig = null): TransferResult;

    public function getOwner(string $uai, ?BlockchainConfig $config = null): ?string;
}

<?php

namespace Dkg\Services\AssetService;

use Dkg\Services\AssetService\Dto\GetOptions;
use Dkg\Services\AssetService\Dto\GetResult;
use Dkg\Services\AssetService\Dto\PublishOptions;
use Dkg\Services\AssetService\Dto\PublishResult;

interface AssetServiceInterface
{
    /**
     * @param array $content
     * @param PublishOptions|null $options
     * @param array $stepHooks
     * @return PublishResult
     */
    public function create(array $content, ?PublishOptions $options, array $stepHooks): PublishResult;

    /**
     * @param string $uai
     * @param GetOptions|null $options
     * @return GetResult
     */
    public function get(string $uai, ?GetOptions $options): GetResult;

    /**
     * @param string $uai
     * @param array $update
     * @param PublishOptions|null $options
     * @return PublishResult
     */
    public function update(string $uai, array $update, ?PublishOptions $options): PublishResult;

    public function transfer();

    public function getOwner();
}

<?php

namespace Dkg\Services\AssetService;

use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\GetOptions;
use Dkg\Services\AssetService\Dto\GetResult;
use Dkg\Services\AssetService\Dto\PublishOptions;

interface AssetServiceInterface
{
    /**
     * @param array $content
     * @param PublishOptions|null $options
     * @param array $stepHooks
     * @return Asset
     */
    public function create(array $content, ?PublishOptions $options, array $stepHooks = []): Asset;

    /**
     * @param string $uai
     * @param GetOptions|null $options
     * @return GetResult
     */
    public function get(string $uai, ?GetOptions $options = null): GetResult;

    public function update();

    public function transfer();

    public function getOwner();
}

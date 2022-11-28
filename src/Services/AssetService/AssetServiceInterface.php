<?php

namespace Dkg\Services\AssetService;

use Dkg\Services\AssetService\Dto\Asset;
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

    public function get();

    public function update();

    public function transfer();

    public function getOwner();
}

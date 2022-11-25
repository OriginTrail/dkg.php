<?php

namespace Dkg\Services\AssetService;

use Dkg\Services\AssetService\Dto\PublishOptions;

interface AssetServiceInterface
{
    public function create(array $content, ?PublishOptions $options, $stepHooks);
    public function get();
    public function update();
    public function transfer();
    public function getOwner();
}

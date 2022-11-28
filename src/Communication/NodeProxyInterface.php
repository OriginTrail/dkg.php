<?php

namespace Dkg\Communication;

use Dkg\Communication\Exceptions\NodeProxyException;
use Dkg\Communication\HttpClient\HttpResponse;
use Dkg\Exceptions\InvalidPublishRequestException;
use Dkg\Exceptions\ServiceMisconfigurationException;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\PublishOptions;

interface NodeProxyInterface
{
    /**
     * @param HttpConfig|null $config
     * @return HttpResponse
     */
    public function info(?HttpConfig $config): HttpResponse;

    /**
     * @param Asset $asset
     * @param PublishOptions $options
     * @return OperationResult
     */
    public function publish(Asset $asset, PublishOptions $options): OperationResult;
}

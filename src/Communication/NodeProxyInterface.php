<?php

namespace Dkg\Communication;

use Dkg\Communication\HttpClient\HttpResponse;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\GetOptions;
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

    /**
     * @param string $uai
     * @param GetOptions $options
     * @return OperationResult
     */
    public function get(string $uai, GetOptions $options): OperationResult;

    public function getBidSuggestion(int $assertionSize, PublishOptions $options);
}

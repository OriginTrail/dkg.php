<?php

namespace Dkg\Communication;

use Dkg\Communication\Infrastructure\Exceptions\CommunicationException;
use Dkg\Communication\Infrastructure\HttpClient\HttpResponse;
use Dkg\Exceptions\ConfigMissingException;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\PublishOptions;

interface NodeProxyInterface
{
    /**
     * @param string|null $baseUrl
     * @param string|null $authToken
     * @return HttpResponse
     * @throws ConfigMissingException
     * @throws CommunicationException
     */
    public function info(?string $baseUrl, ?string $authToken): HttpResponse;

    public function publish(Asset $asset, PublishOptions $options);
}

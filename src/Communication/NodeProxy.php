<?php

namespace Dkg\Communication;

use Dkg\Communication\Infrastructure\Exceptions\CommunicationException;
use Dkg\Communication\Infrastructure\Exceptions\MaximumAttemptsExceededException;
use Dkg\Communication\Infrastructure\HttpClient\HttpClient;
use Dkg\Communication\Infrastructure\HttpClient\HttpClientInterface;
use Dkg\Communication\Infrastructure\HttpClient\HttpResponse;
use Dkg\Exceptions\ConfigMissingException;
use Dkg\Exceptions\InvalidPublishRequestException;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\PublishOptions;
use Dkg\Services\Constants;

class NodeProxy implements NodeProxyInterface
{
    private const FINITE_STATUSES = ['FAILED', 'COMPLETED'];

    /** @var HttpClient */
    private $client;

    /** @var HttpConfig */
    private $config;

    public function __construct(HttpClientInterface $client, ?HttpConfig $config = null)
    {
        $this->client = $client;

        if ($config) {
            $this->config = $config;
        } else {
            // initialize default
            $this->config = new HttpConfig();
        }
    }

    public function info(?string $baseUrl = null, ?string $authToken = null): HttpResponse
    {
        $url = $this->getBaseUrl($baseUrl) . '/info';
        $authToken = $authToken ?? $this->config->getAuthToken();
        $headers = $this->prepareHeaders($authToken);

        return $this->client->get($url, $headers);
    }


    /**
     * @throws MaximumAttemptsExceededException
     * @throws ConfigMissingException
     * @throws CommunicationException
     * @throws InvalidPublishRequestException
     */
    public function publish(Asset $asset, PublishOptions $options): HttpResponse
    {
        $url = $this->getBaseUrl($options->getBaseUrl()) . '/api/post';
        $body = $this->preparePublishBody($asset, $options);
        $headers = $this->prepareHeaders($options->getAuthToken());

        return $this->processAsync($url, $body, $headers);
    }

    /**
     * @param $url
     * @param array $body
     * @param array $headers
     * @return HttpResponse
     * @throws CommunicationException
     * @throws MaximumAttemptsExceededException
     */
    private function processAsync($url, array $body = [], array $headers = []): HttpResponse
    {
        $res = $this->client->post($url, $body, $headers);

        if (!$res->isSuccessful()) {
            return $res;
        }

        $counter = 0;
        $handlerId = $res->getBodyAsObject()->handler_id;

        while ($counter++ < $this->config->getMaxNumOfRetries()) {
            $res = $this->client->get($url . "/result/$handlerId", $headers);

            if (!$res->isSuccessful() || $this->isProcessingFinished($res)) {
                return $res;
            }

            // usleep measures time in microseconds, retryFrequency is in ms
            usleep($this->config->getRetryFrequency() * 1000);
        }

        throw new MaximumAttemptsExceededException("Exceeded {$this->config->getMaxNumOfRetries()} number of retries. Handler id: $handlerId");
    }

    /**
     * Returns BaseURL.
     * Provided baseUrl overrides default baseURL.
     * @param string|null $baseUrl
     * @return string
     * @throws ConfigMissingException
     */
    private function getBaseUrl(?string $baseUrl): string
    {
        if (!$baseUrl && !$this->config->getBaseUrl()) {
            throw new ConfigMissingException('No base URL provided.');
        }

        return $baseUrl ?? $this->config->getBaseUrl();
    }

    /**
     * @param HttpResponse $response
     * @return bool
     */
    private function isProcessingFinished($response): bool
    {
        return in_array($response->getBodyAsObject()->status, self::FINITE_STATUSES);
    }

    /**
     * @throws InvalidPublishRequestException
     */
    private function preparePublishBody(Asset $asset, PublishOptions $options): array
    {
        $baseBody = [
            'publishType' => $options->getPublishType(),
            'assertionId' => $asset->getAssertionId(),
            'assertion' => $asset->getAssertion()
        ];

        switch ($options->getPublishType()) {
            case Constants::PUBLISH_TYPE_ASSET:
                $body = [
                    'blockchain' => $asset->getBlockchain(),
                    'contract' => $asset->getContract(),
                    'tokenId' => $asset->getTokenId(),
                    'hashFunctionId' => $options->getHashFunctionId(),
                    'localStore' => $options->isLocalStore()
                ];
                break;
            default:
                throw new InvalidPublishRequestException("Unsupported publish type '{$options->getPublishType()}'.");
        }

        return array_merge($baseBody, $body);
    }

    private function prepareHeaders(?string $authToken): ?array
    {
        if ($authToken) {
            return [
                'Authorization' => "Bearer $authToken"
            ];
        }

        return [];
    }
}

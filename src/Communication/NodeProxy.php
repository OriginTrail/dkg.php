<?php

namespace Dkg\Communication;

use Dkg\Communication\Exceptions\NodeProxyException;
use Dkg\Communication\HttpClient\HttpClient;
use Dkg\Communication\HttpClient\HttpClientInterface;
use Dkg\Communication\HttpClient\HttpResponse;
use Dkg\Exceptions\InvalidRequestException;
use Dkg\Exceptions\ServiceMisconfigurationException;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\GetOptions;
use Dkg\Services\AssetService\Dto\PublishOptions;
use Dkg\Services\Params;

class NodeProxy implements NodeProxyInterface
{
    private const FINITE_STATUSES = ['FAILED', 'COMPLETED'];

    /** @var HttpClient */
    private $client;

    /** @var HttpConfig */
    private $baseConfig;

    public function __construct(HttpClientInterface $client, ?HttpConfig $config = null)
    {
        $this->client = $client;
        $this->baseConfig = $config;
    }

    /**
     * @throws ServiceMisconfigurationException
     * @throws NodeProxyException
     */
    public function info(?HttpConfig $config = null): HttpResponse
    {
        $config = $this->mergeConfig($config);
        $url = $this->getBaseUrl($config) . '/info';
        $headers = $this->prepareHeaders($config);

        return $this->client->get($url, $headers);
    }

    /**
     * @throws ServiceMisconfigurationException
     * @throws NodeProxyException
     * @throws InvalidRequestException
     */
    public function publish(Asset $asset, PublishOptions $options): OperationResult
    {
        $url = $this->getBaseUrl($options->getHttpConfig()) . '/publish';
        $body = $this->preparePublishBody($asset, $options);
        $headers = $this->prepareHeaders($options->getHttpConfig());

        return $this->processAsync($url, $body, $headers);
    }

    /**
     * @throws InvalidRequestException
     */
    private function preparePublishBody(Asset $asset, PublishOptions $options): array
    {
        $baseBody = [
            'publishType' => $options->getPublishType(),
            'assertionId' => $asset->getAssertionId(),
            'assertion' => $asset->getAssertion()
        ];

        switch ($options->getPublishType()) {
            case Params::PUBLISH_TYPE_ASSET:
                $body = [
                    'blockchain' => $asset->getBlockchain(),
                    'contract' => $asset->getContract(),
                    'tokenId' => $asset->getTokenId(),
                    'hashFunctionId' => $options->getHashFunctionId(),
                    'localStore' => $options->isLocalStore()
                ];
                break;
            default:
                throw new InvalidRequestException("Unsupported publish type '{$options->getPublishType()}'.");
        }

        return array_merge($baseBody, $body);
    }

    /**
     * @throws ServiceMisconfigurationException
     * @throws NodeProxyException
     */
    public function get(string $uai, GetOptions $options): OperationResult
    {
        $url = $this->getBaseUrl($options->getHttpConfig()) . '/get';
        $body = $this->prepareGetBody($uai, $options);
        $headers = $this->prepareHeaders($options->getHttpConfig());

        return $this->processAsync($url, $body, $headers);
    }

    private function prepareGetBody(string $uai, GetOptions $options): array
    {
        return [
            'id' => $uai,
            'hashFunctionId' => $options->getHashFunctionId()
        ];
    }

    public function getBidSuggestion(int $assertionSize, PublishOptions $options)
    {
        $url = $this->getBaseUrl($options->getHttpConfig()) . '/bid-suggestion';
        $headers = $this->prepareHeaders($options->getHttpConfig());
        $reqOptions = array_merge($headers, [
            'query' => [
                'blockchain' => $options->getBlockchainConfig()->getBlockchainName(),
                'epochsNumber' => $options->getEpochsNum(),
                'assertionSize' => $assertionSize
            ]
        ]);

        $response = $this->client->get($url, $reqOptions);
        return $response->getBodyAsObject()->data->bidSuggestion;
    }


    /**
     * @throws ServiceMisconfigurationException
     * @throws NodeProxyException
     */
    public function query(string $query, string $queryType, ?HttpConfig $config): OperationResult
    {
        $url = $this->getBaseUrl($config) . '/query';
        $headers = $this->prepareHeaders($config);
        $body = [
            'query' => $query,
            'type' => $queryType
        ];

        return $this->processAsync($url, $body, $headers);
    }

    /**
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return OperationResult
     * @throws NodeProxyException
     */
    private function processAsync(string $url, array $body = [], array $headers = []): OperationResult
    {
        $res = $this->client->post($url, $body, $headers);

        if (!$res->isSuccessful()) {
            throw new NodeProxyException("Ot Node returned {$res->getStatusCode()} code.", $res);
        }

        $counter = 0;
        $operationId = $res->getBodyAsObject()->operationId;

        while ($counter++ < $this->baseConfig->getMaxNumOfRetries()) {
            $res = $this->client->get($url . "/$operationId", $headers);

            if (!$res->isSuccessful() || $this->isProcessingFinished($res)) {
                $body = $res->getBodyAsObject();
                return new OperationResult(
                    $body->status,
                    $body->data,
                    $operationId
                );
            }

            // usleep measures time in microseconds, retryFrequency is in ms
            usleep($this->baseConfig->getRetryFrequency() * 1000);
        }

        throw new NodeProxyException("Exceeded {$this->baseConfig->getMaxNumOfRetries()} number of retries.", ['operationId' => $operationId]);
    }

    /**
     * Returns BaseURL.
     * Provided baseUrl overrides default baseURL.
     * @param HttpConfig|null $config
     * @return string
     * @throws ServiceMisconfigurationException
     */
    private function getBaseUrl(?HttpConfig $config): string
    {
        if ((!$config || !$config->getBaseUrl()) && !$this->baseConfig->getBaseUrl()) {
            throw new ServiceMisconfigurationException('No base URL provided.');
        }

        if ($config) {
            return $config->getBaseUrl() ?? $this->baseConfig->getBaseUrl();
        }

        return $this->baseConfig->getBaseUrl();
    }

    /**
     * @param HttpConfig|null $config
     * @return array
     */
    private function prepareHeaders(?HttpConfig $config): ?array
    {
        if ((!$config || !$config->getAuthToken()) && !$this->baseConfig->getAuthToken()) {
            return [];
        }

        if ($config) {
            $token = $config->getAuthToken() ?? $this->baseConfig->getAuthToken();
        } else {
            $token = $this->baseConfig->getAuthToken();
        }

        return [
            'headers' => [
                'Authorization' => "Bearer $token"
            ]
        ];
    }

    /**
     * @param HttpResponse $response
     * @return bool
     */
    private function isProcessingFinished(HttpResponse $response): bool
    {
        return in_array($response->getBodyAsObject()->status, self::FINITE_STATUSES);
    }

    /**
     * @throws ServiceMisconfigurationException
     */
    private function mergeConfig(?HttpConfig $config): HttpConfig
    {
        $mergedConfig = new HttpConfig();

        if (!$config && !$this->baseConfig) {
            throw new ServiceMisconfigurationException("No config is provided to NodeProxy");
        }

        if ($this->baseConfig) {
            $mergedConfig->setMaxNumOfRetries($this->baseConfig->getMaxNumOfRetries());
            $mergedConfig->setRetryFrequency($this->baseConfig->getRetryFrequency());
            $mergedConfig->setBaseUrl($this->baseConfig->getBaseUrl());
            $mergedConfig->setAuthToken($this->baseConfig->getAuthToken());
        }

        if ($config) {
            if ($config->getMaxNumOfRetries()) {
                $mergedConfig->setMaxNumOfRetries($config->getMaxNumOfRetries());
            }

            if ($config->getRetryFrequency()) {
                $mergedConfig->setRetryFrequency($config->getRetryFrequency());
            }

            if ($config->getBaseUrl()) {
                $mergedConfig->setBaseUrl($config->getBaseUrl());
            }

            if ($config->getAuthToken()) {
                $mergedConfig->setAuthToken($config->getAuthToken());
            }
        }

        if (!$mergedConfig->validate()) {
            throw new ServiceMisconfigurationException("Missing some of config properties for NodeProxy.");
        }

        return $mergedConfig;
    }
}

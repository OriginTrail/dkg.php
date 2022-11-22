<?php

namespace Dkg\Communication;

use Dkg\Communication\Infrastructure\Exceptions\MaximumAttemptsExceededException;
use Dkg\Communication\Infrastructure\HttpClient\HttpClient;
use Dkg\Communication\Infrastructure\HttpClient\HttpClientInterface;
use Dkg\Communication\Infrastructure\HttpClient\HttpResponse;

class NodeProxy implements NodeProxyInterface
{
    private const MAX_NUMBER_OF_ATTEMPTS = 20;
    private const WAITING_TIME_MILLISECONDS = 500 * 1000; // usleep() takes time in microseconds
    private const FINITE_STATUSES = ['FAILED', 'COMPLETED'];

    /** @var HttpClient */
    private $client;
    /** @var string|null  */
    private $baseURL;

    public function __construct(HttpClientInterface $client, ?string $baseURL)
    {
        $this->client = $client;
        $this->baseURL = $baseURL;
    }

    public function processAsync($url, array $body = [], array $headers = []): HttpResponse
    {
        $res = $this->client->post($this->getUrl($url), $body, $headers);

        if (!$res->isSuccessful()) {
            return $res;
        }

        $counter = 0;
        $handlerId = $res->getBodyAsObject()->handler_id;

        while ($counter++ < self::MAX_NUMBER_OF_ATTEMPTS) {
            $res = $this->client->get($url . "/result/$handlerId", $headers);

            if (!$res->isSuccessful() || $this->isProcessingFinished($res)) {
                return $res;
            }

            usleep(self::WAITING_TIME_MILLISECONDS);
        }

        throw new MaximumAttemptsExceededException();
    }

    public function sendRequest($method, $url, array $headers = [], array $body = []): HttpResponse
    {
        return $this->client->sendRequest($method, $this->getUrl($url), $headers, $body);
    }

    /**
     * Prepends baseURL to provided url if set
     * @param $url
     * @return string
     */
    private function getUrl($url): string
    {
        if(isset($this->baseURL)) {
            return $this->baseURL . $url;
        }

        return $url;
    }

    /**
     * @param HttpResponse $response
     * @return bool
     */
    private function isProcessingFinished($response): bool
    {
        return in_array($response->getBodyAsObject()->status, self::FINITE_STATUSES);
    }
}

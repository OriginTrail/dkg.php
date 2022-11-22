<?php

namespace Dkg\Communication;

use Dkg\Communication\Infrastructure\Exceptions\CommunicationException;
use Dkg\Communication\Infrastructure\Exceptions\MaximumAttemptsExceededException;
use Dkg\Communication\Infrastructure\HttpClient\HttpResponse;

interface NodeProxyInterface
{
    /**
     * @param $url
     * @param array $body
     * @param array $headers
     * @return HttpResponse
     * @throws MaximumAttemptsExceededException Node didn't respond in defined number of tries
     * @throws CommunicationException
     */
    public function processAsync($url, array $body = [], array $headers = []): HttpResponse;

    /**
     * @param $method
     * @param $url
     * @param array $headers
     * @param array $body
     * @return HttpResponse
     * @throws CommunicationException
     */
    public function sendRequest($method, $url, array $headers = [], array $body = []): HttpResponse;
}

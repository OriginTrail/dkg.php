<?php

namespace Dkg\Communication\Infrastructure\HttpClient;

use Dkg\Communication\Infrastructure\Exceptions\CommunicationException;

interface HttpClientInterface
{
    /**
     * @param string $method
     * @param number $url
     * @param array $headers
     * @param array $body
     * @return HttpResponse
     * @throws CommunicationException
     */
    public function sendRequest(string $method, string $url, array $headers = [], array $body = []): HttpResponse;

    /**
     * @throws CommunicationException
     */
    public function get(string $url, $headers = []): HttpResponse;

    /**
     * @throws CommunicationException
     */
    public function post(string $url, $body = [], $headers = []): HttpResponse;

    /**
     * @throws CommunicationException
     */
    public function put(string $url, $body = [], $headers = []): HttpResponse;

    /**
     * @throws CommunicationException
     */
    public function delete(string $url, $body = [], $headers = []): HttpResponse;

    /**
     * @throws CommunicationException
     */
    public function patch(string $url, $body = [], $headers = []): HttpResponse;

}

<?php

namespace Dkg\Communication\HttpClient;

use Dkg\Communication\Exceptions\NodeProxyException;

interface HttpClientInterface
{
    /**
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param array $body
     * @return HttpResponse
     * @throws NodeProxyException
     */
    public function sendRequest(string $method, string $url, array $headers = [], array $body = []): HttpResponse;

    /**
     * @throws NodeProxyException
     */
    public function get(string $url, $headers = []): HttpResponse;

    /**
     * @throws NodeProxyException
     */
    public function post(string $url, $body = [], $headers = []): HttpResponse;

    /**
     * @throws NodeProxyException
     */
    public function put(string $url, $body = [], $headers = []): HttpResponse;

    /**
     * @throws NodeProxyException
     */
    public function delete(string $url, $body = [], $headers = []): HttpResponse;

    /**
     * @throws NodeProxyException
     */
    public function patch(string $url, $body = [], $headers = []): HttpResponse;

}

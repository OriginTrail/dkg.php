<?php

namespace Dkg\Communication\HttpClient;

use Dkg\Communication\Exceptions\NodeProxyException;

interface HttpClientInterface
{
    /**
     * @param string $method
     * @param string $url
     * @param array $options
     * @param array $body
     * @return HttpResponse
     */
    public function sendRequest(string $method, string $url, array $options = [], array $body = []): HttpResponse;

    public function get(string $url, $options = []): HttpResponse;

    public function post(string $url, $body = [], $headers = []): HttpResponse;

    public function put(string $url, $body = [], $headers = []): HttpResponse;

    public function delete(string $url, $body = [], $headers = []): HttpResponse;

    public function patch(string $url, $body = [], $headers = []): HttpResponse;

}

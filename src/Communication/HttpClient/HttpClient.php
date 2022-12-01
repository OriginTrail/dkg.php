<?php


namespace Dkg\Communication\HttpClient;

use Dkg\Communication\Exceptions\NodeProxyException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;

/**
 * Wrapper class for HTTP client library
 */
class HttpClient implements HttpClientInterface
{
    const TIMEOUT_IN_SECONDS = 60;

    private $client;


    public function __construct()
    {
        $this->client = new Client();
    }

    public function sendRequest(string $method, string $url, array $reqOptions = [], array $body = []): HttpResponse
    {
        $options['headers'] = $this->getDefaultHeaders();

        if (isset($reqOptions['headers'])) {
            $options['headers'] = array_merge($options['headers'], $reqOptions['headers']);
        }

        if (isset($reqOptions['query'])) {
            $options['query'] = $reqOptions['query'];
        }

        if (count($body)) {
            $body = ['json' => $body];
        }

        try {
            $response = $this->client->request($method, $url, array_merge($options, $body));

            return new HttpResponse(
                $response->getBody()->getContents(),
                $response->getStatusCode(),
                $response->getHeaders()
            );
        } catch (BadResponseException|ServerException $e) {
            return new HttpResponse(
                $e->getResponse()->getBody()->getContents(),
                $e->getResponse()->getStatusCode(),
                $e->getResponse()->getHeaders()
            );
        } catch (GuzzleException $e) {
            throw new NodeProxyException($e->getMessage());
        }
    }

    private function getDefaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'timeout' => self::TIMEOUT_IN_SECONDS
        ];
    }

    /**
     * @throws NodeProxyException
     */
    public function get(string $url, $options = []): HttpResponse
    {
        return $this->sendRequest(HttpMethods::GET_METHOD, $url, $options);
    }

    /**
     * @throws NodeProxyException
     */
    public function post(string $url, $body = [], $headers = []): HttpResponse
    {
        return $this->sendRequest(HttpMethods::POST_METHOD, $url, $headers, $body);
    }

    /**
     * @throws NodeProxyException
     */
    public function put(string $url, $body = [], $headers = []): HttpResponse
    {
        return $this->sendRequest(HttpMethods::PUT_METHOD, $url, $headers, $body);
    }

    /**
     * @throws NodeProxyException
     */
    public function delete(string $url, $body = [], $headers = []): HttpResponse
    {
        return $this->sendRequest(HttpMethods::DELETE_METHOD, $url, $headers, $body);
    }

    /**
     * @throws NodeProxyException
     */
    public function patch(string $url, $body = [], $headers = []): HttpResponse
    {
        return $this->sendRequest(HttpMethods::PATCH_METHOD, $url, $headers, $body);
    }
}

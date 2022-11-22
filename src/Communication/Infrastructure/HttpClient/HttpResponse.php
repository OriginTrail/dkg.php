<?php

namespace Dkg\Communication\Infrastructure\HttpClient;

class HttpResponse
{
    private $body;
    private $statusCode;
    private $headers;

    /**
     * HttpResponse constructor.
     * @param string $body
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct(string $body, int $statusCode, array $headers)
    {
        $this->body = $body;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Returns response body as string
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }

    /**
     * Returns body as object.
     * @return object|null
     */
    public function getBodyAsObject(): ?object
    {
        return json_decode($this->getBody());
    }

    /**
     * Returns body as associative array.
     * @return array|null
     */
    public function getBodyAsArray(): ?array
    {
        return json_decode($this->getBody(), true);
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     */
    public function setStatusCode($statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * Returns indicator whether request is successful.
     * Request is considered successful if response status is in interval [200, 300)
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }
}

<?php

declare(strict_types=1);

namespace Weblabel\HttpClient;

use Psr\Http\Message\RequestInterface;

interface RequestFactory
{
    /**
     * @param mixed $body
     */
    public function createRequest(string $method, string $path, array $queryParameters = [], $body = null, array $headers = []): RequestInterface;

    public function get(string $path, array $queryParameters = [], array $headers = []): RequestInterface;

    /**
     * @param mixed $body
     */
    public function post(string $path, array $queryParameters = [], $body = null, array $headers = []): RequestInterface;

    /**
     * @param mixed $body
     */
    public function put(string $path, array $queryParameters = [], $body = null, array $headers = []): RequestInterface;

    /**
     * @param mixed $body
     */
    public function patch(string $path, array $queryParameters = [], $body = null, array $headers = []): RequestInterface;

    /**
     * @param mixed $body
     */
    public function delete(string $path, array $queryParameters = [], $body = null, array $headers = []): RequestInterface;
}

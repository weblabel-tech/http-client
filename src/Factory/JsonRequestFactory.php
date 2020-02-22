<?php

declare(strict_types=1);

namespace Weblabel\HttpClient\Factory;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Weblabel\HttpClient\Exception\InvalidBodyException;
use Weblabel\HttpClient\RequestFactory;

final class JsonRequestFactory implements RequestFactory
{
    private const CONTENT_TYPE_JSON = 'application/json';

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    public function __construct(RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory)
    {
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidBodyException
     * @throw \JsonException
     */
    public function createRequest(string $method, string $path, array $queryParameters = [], $body = null, array $headers = []): RequestInterface
    {
        $uri = $this->getUri($path, $queryParameters);

        $request = $this->requestFactory->createRequest($method, $uri);

        if (null !== $body) {
            $this->validateBody($body);

            $stream = $this->streamFactory->createStream(\json_encode($body, \JSON_THROW_ON_ERROR));
            $request = $request->withBody($stream);
        }

        if (!empty($headers)) {
            foreach ($headers as $header => $value) {
                $request = $request->withHeader($header, $value);
            }
        }

        $request = $request->withHeader('Content-Type', self::CONTENT_TYPE_JSON);

        return $request;
    }

    public function get(string $path, array $queryParameters = [], array $headers = []): RequestInterface
    {
        return $this->createRequest('GET', $path, $queryParameters, null, $headers);
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidBodyException
     * @throw \JsonException
     */
    public function post(string $path, array $queryParameters = [], $body = null, array $headers = []): RequestInterface
    {
        return $this->createRequest('POST', $path, $queryParameters, $body, $headers);
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidBodyException
     * @throw \JsonException
     */
    public function put(string $path, array $queryParameters = [], $body = null, array $headers = []): RequestInterface
    {
        return $this->createRequest('PUT', $path, $queryParameters, $body, $headers);
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidBodyException
     * @throw \JsonException
     */
    public function patch(string $path, array $queryParameters = [], $body = null, array $headers = []): RequestInterface
    {
        return $this->createRequest('PATCH', $path, $queryParameters, $body, $headers);
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidBodyException
     * @throw \JsonException
     */
    public function delete(string $path, array $queryParameters = [], $body = null, array $headers = []): RequestInterface
    {
        return $this->createRequest('DELETE', $path, $queryParameters, $body, $headers);
    }

    private function getUri(string $path, array $queryParameters): string
    {
        if (!empty($queryParameters)) {
            $path .= '?'.\http_build_query($queryParameters);
        }

        return $path;
    }

    /**
     * @param mixed $body
     *
     * @throws InvalidBodyException
     */
    private function validateBody($body): void
    {
        if (null === $body) {
            return;
        }

        if (!$body instanceof \JsonSerializable && !$body instanceof \stdClass && !\is_array($body)) {
            throw InvalidBodyException::forInvalidBody();
        }
    }
}

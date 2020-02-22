<?php

declare(strict_types=1);

namespace Weblabel\HttpClient;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class HttpClient implements ClientInterface
{
    private ClientInterface $client;

    /** @var RequestMiddleware[] */
    private array $requestMiddleware = [];

    public function __construct(ClientInterface $client, array $requestMiddleware = [])
    {
        $this->client = $client;

        foreach ($requestMiddleware as $middleware) {
            $this->addRequestMiddleware($middleware);
        }
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        foreach ($this->requestMiddleware as $requestMiddleware) {
            $request = $requestMiddleware->process($request);
        }

        return $this->client->sendRequest($request);
    }

    public function addRequestMiddleware(RequestMiddleware $requestMiddleware): void
    {
        $this->requestMiddleware[] = $requestMiddleware;
    }
}

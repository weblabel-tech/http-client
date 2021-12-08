<?php

declare(strict_types=1);

namespace Weblabel\HttpClient\Tests;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Weblabel\HttpClient\HttpClient;
use Weblabel\HttpClient\RequestMiddleware;

class HttpClientTest extends TestCase
{
    public function testExecutionOfInjectedMiddleware()
    {
        $psrFactory = new Psr17Factory();

        $initialRequest = $psrFactory->createRequest('GET', 'https://example.com');
        $requestAfterFirstMiddleware = $psrFactory->createRequest('GET', 'https://example.com')->withHeader('Authorization', 'Bearer token');

        $firstRequestMiddleware = $this->createMock(RequestMiddleware::class);
        $firstRequestMiddleware
            ->expects(self::once())
            ->method('process')
            ->with($initialRequest)
            ->willReturn($requestAfterFirstMiddleware);

        $requestAfterSecondMiddleware = $psrFactory->createRequest('GET', 'https://example.com')->withHeader('Authorization', 'Bearer token')->withHeader('User-Agent', 'test');

        $secondRequestMiddleware = $this->createMock(RequestMiddleware::class);
        $secondRequestMiddleware
            ->expects(self::once())
            ->method('process')
            ->with($requestAfterFirstMiddleware)
            ->willReturn($requestAfterSecondMiddleware);

        $response = $psrFactory->createResponse();

        $coreClient = $this->createMock(ClientInterface::class);
        $coreClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with($requestAfterSecondMiddleware)
            ->willReturn($response);

        $client = new HttpClient($coreClient, [$firstRequestMiddleware, $secondRequestMiddleware]);
        $clientResponse = $client->sendRequest($initialRequest);

        self::assertSame($clientResponse, $response);
    }
}

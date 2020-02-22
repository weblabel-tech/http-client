<?php

declare(strict_types=1);

namespace spec\Weblabel\HttpClient;

use Nyholm\Psr7\Factory\Psr17Factory;
use PhpSpec\ObjectBehavior;
use Psr\Http\Client\ClientInterface;
use Weblabel\HttpClient\RequestMiddleware;

class HttpClientSpec extends ObjectBehavior
{
    public function let(ClientInterface $client)
    {
        $this->beConstructedWith($client, []);
    }

    public function it_implements_psr_interface()
    {
        $this->shouldImplement(ClientInterface::class);
    }

    public function it_executes_injected_middlware(ClientInterface $client, RequestMiddleware $firstRequestMiddleware, RequestMiddleware $secondRequestMiddleware)
    {
        $psrFactory = new Psr17Factory();
        $initialRequest = $psrFactory->createRequest('GET', 'https://example.com');
        $requestAfterFirstMiddleware = $psrFactory->createRequest('GET', 'https://example.com')->withHeader('Authorization', 'Bearer token');
        $requestAfterSecondMiddleware = $psrFactory->createRequest('GET', 'https://example.com')->withHeader('Authorization', 'Bearer token')->withHeader('User-Agent', 'test');

        $firstRequestMiddleware->process($initialRequest)->shouldBeCalledOnce()->willReturn($requestAfterFirstMiddleware);
        $secondRequestMiddleware->process($requestAfterFirstMiddleware)->shouldBeCalledOnce()->willReturn($requestAfterSecondMiddleware);

        $response = $psrFactory->createResponse();
        $client->sendRequest($requestAfterSecondMiddleware)->shouldBeCalledOnce()->willReturn($response);

        $this->beConstructedWith($client, [$firstRequestMiddleware, $secondRequestMiddleware]);
        $this->sendRequest($initialRequest)->shouldBe($response);
    }
}

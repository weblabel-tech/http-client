<?php

declare(strict_types=1);

namespace spec\Weblabel\HttpClient\Factory;

use Nyholm\Psr7\Factory\Psr17Factory;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Weblabel\HttpClient\Exception\InvalidBodyException;
use Weblabel\HttpClient\RequestFactory;

class JsonRequestFactorySpec extends ObjectBehavior
{
    public function let(RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory)
    {
        $this->beConstructedWith($requestFactory, $streamFactory);
    }

    public function it_implements_request_factory_interface()
    {
        $this->shouldImplement(RequestFactory::class);
    }

    public function it_creates_request_with_predefined_json_content_type()
    {
        $psrFactory = new Psr17Factory();
        $this->beConstructedWith($psrFactory, $psrFactory);

        $request = $this->createRequest('GET', '/');
        $request->getHeaderLine('Content-Type')->shouldBe('application/json');
    }

    public function it_allows_to_create_request_with_query_parameters()
    {
        $psrFactory = new Psr17Factory();
        $this->beConstructedWith($psrFactory, $psrFactory);

        $request = $this->createRequest('GET', '/', ['foo' => 'bar']);
        $request->getUri()->getQuery()->shouldBe('foo=bar');
    }

    public function it_allows_to_create_request_with_body()
    {
        $psrFactory = new Psr17Factory();
        $this->beConstructedWith($psrFactory, $psrFactory);

        $request = $this->createRequest('POST', '/', [], ['foo' => 'bar']);
        $request->getBody()->__toString()->shouldBe('{"foo":"bar"}');
    }

    public function it_allows_to_create_request_with_headers()
    {
        $psrFactory = new Psr17Factory();
        $this->beConstructedWith($psrFactory, $psrFactory);

        $request = $this->createRequest('GET', '/', [], null, ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);
        $request->getHeaderLine('Authorization')->shouldBe('Bearer token');
        $request->getHeaderLine('User-Agent')->shouldBe('test');
    }

    public function it_allows_to_create_get_request()
    {
        $psrFactory = new Psr17Factory();
        $this->beConstructedWith($psrFactory, $psrFactory);

        $request = $this->get('/', ['foo' => 'bar'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);
        $request->getMethod()->shouldBe('GET');
        $request->getUri()->getPath()->shouldBe('/');
        $request->getUri()->getQuery()->shouldBe('foo=bar');
        $request->getHeaderLine('Authorization')->shouldBe('Bearer token');
        $request->getHeaderLine('User-Agent')->shouldBe('test');
    }

    public function it_allows_to_create_post_request()
    {
        $psrFactory = new Psr17Factory();
        $this->beConstructedWith($psrFactory, $psrFactory);

        $request = $this->post('/', ['foo' => 'bar'], ['baz' => 'qux'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);
        $request->getMethod()->shouldBe('POST');
        $request->getUri()->getPath()->shouldBe('/');
        $request->getUri()->getQuery()->shouldBe('foo=bar');
        $request->getBody()->__toString()->shouldBe('{"baz":"qux"}');
        $request->getHeaderLine('Authorization')->shouldBe('Bearer token');
        $request->getHeaderLine('User-Agent')->shouldBe('test');
    }

    public function it_allows_to_create_put_request()
    {
        $psrFactory = new Psr17Factory();
        $this->beConstructedWith($psrFactory, $psrFactory);

        $request = $this->put('/', ['foo' => 'bar'], ['baz' => 'qux'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);
        $request->getMethod()->shouldBe('PUT');
        $request->getUri()->getPath()->shouldBe('/');
        $request->getUri()->getQuery()->shouldBe('foo=bar');
        $request->getBody()->__toString()->shouldBe('{"baz":"qux"}');
        $request->getHeaderLine('Authorization')->shouldBe('Bearer token');
        $request->getHeaderLine('User-Agent')->shouldBe('test');
    }

    public function it_allows_to_create_patch_request()
    {
        $psrFactory = new Psr17Factory();
        $this->beConstructedWith($psrFactory, $psrFactory);

        $request = $this->patch('/', ['foo' => 'bar'], ['baz' => 'qux'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);
        $request->getMethod()->shouldBe('PATCH');
        $request->getUri()->getPath()->shouldBe('/');
        $request->getUri()->getQuery()->shouldBe('foo=bar');
        $request->getBody()->__toString()->shouldBe('{"baz":"qux"}');
        $request->getHeaderLine('Authorization')->shouldBe('Bearer token');
        $request->getHeaderLine('User-Agent')->shouldBe('test');
    }

    public function it_allows_to_create_delete_request()
    {
        $psrFactory = new Psr17Factory();
        $this->beConstructedWith($psrFactory, $psrFactory);

        $request = $this->delete('/', ['foo' => 'bar'], ['baz' => 'qux'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);
        $request->getMethod()->shouldBe('DELETE');
        $request->getUri()->getPath()->shouldBe('/');
        $request->getUri()->getQuery()->shouldBe('foo=bar');
        $request->getBody()->__toString()->shouldBe('{"baz":"qux"}');
        $request->getHeaderLine('Authorization')->shouldBe('Bearer token');
        $request->getHeaderLine('User-Agent')->shouldBe('test');
    }

    public function it_fails_if_body_is_not_encodable()
    {
        $psrFactory = new Psr17Factory();
        $this->beConstructedWith($psrFactory, $psrFactory);

        $this->shouldThrow(InvalidBodyException::class)->duringCreateRequest('POST', '/', [], false);
    }

    public function it_fails_if_body_contains_invalid_characters()
    {
        $psrFactory = new Psr17Factory();
        $this->beConstructedWith($psrFactory, $psrFactory);

        $this->shouldThrow(\JsonException::class)->duringCreateRequest('POST', '/', [], ['foo' => "\xC0"]);
    }
}

<?php

declare(strict_types=1);

namespace Weblabel\HttpClient\Tests\Factory;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Weblabel\HttpClient\Exception\InvalidBodyException;
use Weblabel\HttpClient\Factory\JsonRequestFactory;

class JsonRequestFactoryTest extends TestCase
{
    private static JsonRequestFactory $jsonRequestFactory;

    public static function setUpBeforeClass(): void
    {
        $psrFactory = new Psr17Factory();

        self::$jsonRequestFactory = new JsonRequestFactory($psrFactory, $psrFactory);
    }

    public function test_request_creation_with_predefined_json_content_type()
    {
        $request = self::$jsonRequestFactory->createRequest('GET', '/');

        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
    }

    public function test_request_creation_with_query_parameters()
    {
        $request = self::$jsonRequestFactory->createRequest('GET', '/', ['foo' => 'bar']);

        self::assertSame('foo=bar', $request->getUri()->getQuery());
    }

    public function test_request_creation_with_valid_body()
    {
        $request = self::$jsonRequestFactory->createRequest('POST', '/', [], ['foo' => 'bar']);

        self::assertSame('{"foo":"bar"}', (string) $request->getBody());
    }

    public function test_request_creation_with_headers()
    {
        $request = self::$jsonRequestFactory->createRequest('GET', '/', [], null, ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);

        self::assertSame('Bearer token', $request->getHeaderLine('Authorization'));
        self::assertSame('test', $request->getHeaderLine('User-Agent'));
    }

    public function test_get_request_creation()
    {
        $request = self::$jsonRequestFactory->get('/', ['foo' => 'bar'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);

        self::assertSame('GET', $request->getMethod());
        self::assertSame('/', $request->getUri()->getPath());
        self::assertSame('foo=bar', $request->getUri()->getQuery());
        self::assertSame('Bearer token', $request->getHeaderLine('Authorization'));
        self::assertSame('test', $request->getHeaderLine('User-Agent'));
    }

    public function test_post_request_creation()
    {
        $request = self::$jsonRequestFactory->post('/', ['foo' => 'bar'], ['baz' => 'qux'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);

        self::assertSame('POST', $request->getMethod());
        self::assertSame('/', $request->getUri()->getPath());
        self::assertSame('foo=bar', $request->getUri()->getQuery());
        self::assertSame('{"baz":"qux"}', $request->getBody()->__toString());
        self::assertSame('Bearer token', $request->getHeaderLine('Authorization'));
        self::assertSame('test', $request->getHeaderLine('User-Agent'));
    }

    public function test_put_request_creation()
    {
        $request = self::$jsonRequestFactory->put('/', ['foo' => 'bar'], ['baz' => 'qux'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);

        self::assertSame('PUT', $request->getMethod());
        self::assertSame('/', $request->getUri()->getPath());
        self::assertSame('foo=bar', $request->getUri()->getQuery());
        self::assertSame('{"baz":"qux"}', $request->getBody()->__toString());
        self::assertSame('Bearer token', $request->getHeaderLine('Authorization'));
        self::assertSame('test', $request->getHeaderLine('User-Agent'));
    }

    public function test_patch_request_creation()
    {
        $request = self::$jsonRequestFactory->patch('/', ['foo' => 'bar'], ['baz' => 'qux'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);

        self::assertSame('PATCH', $request->getMethod());
        self::assertSame('/', $request->getUri()->getPath());
        self::assertSame('foo=bar', $request->getUri()->getQuery());
        self::assertSame('{"baz":"qux"}', $request->getBody()->__toString());
        self::assertSame('Bearer token', $request->getHeaderLine('Authorization'));
        self::assertSame('test', $request->getHeaderLine('User-Agent'));
    }

    public function test_delete_request_creation()
    {
        $request = self::$jsonRequestFactory->delete('/', ['foo' => 'bar'], ['baz' => 'qux'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);

        self::assertSame('DELETE', $request->getMethod());
        self::assertSame('/', $request->getUri()->getPath());
        self::assertSame('foo=bar', $request->getUri()->getQuery());
        self::assertSame('{"baz":"qux"}', $request->getBody()->__toString());
        self::assertSame('Bearer token', $request->getHeaderLine('Authorization'));
        self::assertSame('test', $request->getHeaderLine('User-Agent'));
    }

    public function test_request_creation_with_non_encodable_payload()
    {
        $this->expectException(InvalidBodyException::class);

        self::$jsonRequestFactory->createRequest('POST', '/', [], false);
    }

    public function test_request_creation_with_non_encodable_character()
    {
        $this->expectException(\JsonException::class);

        self::$jsonRequestFactory->createRequest('POST', '/', [], ['foo' => "\xC0"]);
    }
}

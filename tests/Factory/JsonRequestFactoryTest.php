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

    public function testRequestCreationWithPredefinedJsonContentType()
    {
        $request = self::$jsonRequestFactory->createRequest('GET', '/');

        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
    }

    public function testRequestCreationWithQueryParameters()
    {
        $request = self::$jsonRequestFactory->createRequest('GET', '/', ['foo' => 'bar']);

        self::assertSame('foo=bar', $request->getUri()->getQuery());
    }

    public function testRequestCreationWithValidBody()
    {
        $request = self::$jsonRequestFactory->createRequest('POST', '/', [], ['foo' => 'bar']);

        self::assertSame('{"foo":"bar"}', (string) $request->getBody());
    }

    public function testRequestCreationWithHeaders()
    {
        $request = self::$jsonRequestFactory->createRequest('GET', '/', [], null, ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);

        self::assertSame('Bearer token', $request->getHeaderLine('Authorization'));
        self::assertSame('test', $request->getHeaderLine('User-Agent'));
    }

    public function testGetRequestCreation()
    {
        $request = self::$jsonRequestFactory->get('/', ['foo' => 'bar'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);

        self::assertSame('GET', $request->getMethod());
        self::assertSame('/', $request->getUri()->getPath());
        self::assertSame('foo=bar', $request->getUri()->getQuery());
        self::assertSame('Bearer token', $request->getHeaderLine('Authorization'));
        self::assertSame('test', $request->getHeaderLine('User-Agent'));
    }

    public function testPostRequestCreation()
    {
        $request = self::$jsonRequestFactory->post('/', ['foo' => 'bar'], ['baz' => 'qux'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);

        self::assertSame('POST', $request->getMethod());
        self::assertSame('/', $request->getUri()->getPath());
        self::assertSame('foo=bar', $request->getUri()->getQuery());
        self::assertSame('{"baz":"qux"}', $request->getBody()->__toString());
        self::assertSame('Bearer token', $request->getHeaderLine('Authorization'));
        self::assertSame('test', $request->getHeaderLine('User-Agent'));
    }

    public function testPutRequestCreation()
    {
        $request = self::$jsonRequestFactory->put('/', ['foo' => 'bar'], ['baz' => 'qux'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);

        self::assertSame('PUT', $request->getMethod());
        self::assertSame('/', $request->getUri()->getPath());
        self::assertSame('foo=bar', $request->getUri()->getQuery());
        self::assertSame('{"baz":"qux"}', $request->getBody()->__toString());
        self::assertSame('Bearer token', $request->getHeaderLine('Authorization'));
        self::assertSame('test', $request->getHeaderLine('User-Agent'));
    }

    public function testPatchRequestCreation()
    {
        $request = self::$jsonRequestFactory->patch('/', ['foo' => 'bar'], ['baz' => 'qux'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);

        self::assertSame('PATCH', $request->getMethod());
        self::assertSame('/', $request->getUri()->getPath());
        self::assertSame('foo=bar', $request->getUri()->getQuery());
        self::assertSame('{"baz":"qux"}', $request->getBody()->__toString());
        self::assertSame('Bearer token', $request->getHeaderLine('Authorization'));
        self::assertSame('test', $request->getHeaderLine('User-Agent'));
    }

    public function testDeleteRequestCreation()
    {
        $request = self::$jsonRequestFactory->delete('/', ['foo' => 'bar'], ['baz' => 'qux'], ['Authorization' => 'Bearer token', 'User-Agent' => 'test']);

        self::assertSame('DELETE', $request->getMethod());
        self::assertSame('/', $request->getUri()->getPath());
        self::assertSame('foo=bar', $request->getUri()->getQuery());
        self::assertSame('{"baz":"qux"}', $request->getBody()->__toString());
        self::assertSame('Bearer token', $request->getHeaderLine('Authorization'));
        self::assertSame('test', $request->getHeaderLine('User-Agent'));
    }

    public function testRequestCreationWithNonEncodablePayload()
    {
        $this->expectException(InvalidBodyException::class);

        self::$jsonRequestFactory->createRequest('POST', '/', [], false);
    }

    public function testRequestCreationWithNonEncodableCharacter()
    {
        $this->expectException(\JsonException::class);

        self::$jsonRequestFactory->createRequest('POST', '/', [], ['foo' => "\xC0"]);
    }
}

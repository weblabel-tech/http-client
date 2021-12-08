<?php

declare(strict_types=1);

namespace Weblabel\HttpClient\Tests;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Weblabel\HttpClient\Exception\HttpException;
use Weblabel\HttpClient\HttpExceptionTrait;

class HttpExceptionTraitTest extends TestCase
{
    private static Psr17Factory $psrFactory;

    public static function setUpBeforeClass(): void
    {
        self::$psrFactory = new Psr17Factory();
    }

    private static function getMappedResponseCodes(array $responseCodes): array
    {
        return array_map(
            static function (int $responseCode) {
                return [$responseCode];
            },
            $responseCodes
        );
    }

    /**
     * @dataProvider errorCodeDataProvider
     */
    public function testThrowingExceptionOnErrorResponseCode(int $responseCode)
    {
        $this->expectException(HttpException::class);

        /** @var HttpExceptionTrait $trait */
        $trait = $this->getMockForTrait(HttpExceptionTrait::class);
        $trait->throwExceptionOnFailure(self::$psrFactory->createRequest('GET', 'https://example.com'), self::$psrFactory->createResponse($responseCode));
    }

    /**
     * @dataProvider successCodeDataProvider
     */
    public function testThrowingExceptionOnSuccessResponseCode(int $responseCode)
    {
        /** @var HttpExceptionTrait $trait */
        $trait = $this->getMockForTrait(HttpExceptionTrait::class);
        $trait->throwExceptionOnFailure(self::$psrFactory->createRequest('GET', 'https://example.com'), self::$psrFactory->createResponse($responseCode));

        self::assertTrue(true);
    }

    public function errorCodeDataProvider()
    {
        return self::getMappedResponseCodes(range(400, 599));
    }

    public function successCodeDataProvider()
    {
        return self::getMappedResponseCodes(range(100, 399));
    }
}

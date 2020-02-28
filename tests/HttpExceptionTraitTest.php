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

    /**
     * @dataProvider errorCodeDataProvider
     */
    public function test_throwing_exception_on_error_response_code(int $responseCode)
    {
        $this->expectException(HttpException::class);

        /** @var HttpExceptionTrait $trait */
        $trait = $this->getMockForTrait(HttpExceptionTrait::class);
        $trait->throwExceptionOnFailure(self::$psrFactory->createRequest('GET', 'https://example.com'), self::$psrFactory->createResponse($responseCode));
    }

    public function errorCodeDataProvider()
    {
        $data = \range(400, 599);

        return \array_map(
            static function (int $errorCode) {
                return [$errorCode];
            },
            $data
        );
    }
}

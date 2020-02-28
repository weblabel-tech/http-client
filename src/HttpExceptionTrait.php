<?php

declare(strict_types=1);

namespace Weblabel\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Weblabel\HttpClient\Exception\HttpException;

trait HttpExceptionTrait
{
    public function throwExceptionOnFailure(RequestInterface $request, ResponseInterface $response): void
    {
        if (400 <= $response->getStatusCode()) {
            throw new HttpException($request, $response);
        }
    }
}

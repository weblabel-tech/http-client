<?php

declare(strict_types=1);

namespace Weblabel\HttpClient;

use Psr\Http\Message\RequestInterface;

interface RequestMiddleware
{
    public function process(RequestInterface $request): RequestInterface;
}

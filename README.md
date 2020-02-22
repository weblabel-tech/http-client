HTTP Client
============
[![Build Status](https://travis-ci.org/weblabel-tech/http-client.svg?branch=master)](https://travis-ci.org/weblabel-tech/http-client)

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Open a command console, enter your project directory and execute:

```console
$ composer require weblabel/http-client
```

This package uses PSR-7, PSR-17 and PSR-18 interfaces, but does not provide their implementations,
so you have to install libraries that implements those.

Recommended libraries:
 - [Nyholm PSR-7](https://github.com/Nyholm/psr7/)
 - [Symfony HttpClient](https://github.com/symfony/http-client)

```console
$ composer require nyholm/psr7
```

```console
$ composer require symfony/http-client
```

Basic Usage
===========
```php
<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;
use Weblabel\HttpClient\HttpClient;
use Weblabel\HttpClient\RequestFactory;
use Weblabel\HttpClient\Factory\JsonRequestFactory;

$symfonyClient = new Psr18Client();
$httpClient = new HttpClient($symfonyClient);
$psrFactory = new Psr17Factory();
$jsonRequestFactory = new JsonRequestFactory($psrFactory, $psrFactory);

$simpleClient = new SimpleClient('https://example.com', $httpClient, $jsonRequestFactory);
$statusResponse = $simpleClient->getStatus();

class SimpleClient
{
    private ClientInterface $client;
    private RequestFactory $requestFactory;
    private string $baseUri;

    public function __construct(string $baseUri, ClientInterface $client, RequestFactory $requestFactory)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->baseUri = $baseUri;
    }

    public function getStatus(): ResponseInterface
    {
        $request = $this->requestFactory->get($this->baseUri . '/status');

        return $this->client->sendRequest($request);
    }
}
```

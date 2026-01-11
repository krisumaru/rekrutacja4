<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface TransportInterface
{
    public function sendRequest(RequestInterface $request): ResponseInterface;
}

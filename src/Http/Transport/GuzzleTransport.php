<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Http\Transport;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use rekrutacja4\RestClient\Exception\ApiException;
use rekrutacja4\RestClient\Http\TransportInterface;

class GuzzleTransport implements TransportInterface
{
    private GuzzleClientInterface $client;

    public function __construct(GuzzleClientInterface $client)
    {
        $this->client = $client;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->client->send($request);
        } catch (GuzzleException $e) {
            throw new ApiException('HTTP client error: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}

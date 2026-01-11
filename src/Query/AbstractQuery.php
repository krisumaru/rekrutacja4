<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Query;

use Psr\Http\Message\ResponseInterface;
use rekrutacja4\RestClient\Exception\ApiException;
use rekrutacja4\RestClient\Http\ClientInterface;

abstract class AbstractQuery
{
    protected string $baseUri;

    public function __construct(
        private readonly ClientInterface $client
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    protected function get(string $path): array
    {
        $response = $this->client->get($path);
        return $this->handleResponse($response);
    }

    /**
     * @return array<string, mixed>
     */
    protected function handleResponse(ResponseInterface $response): array
    {
        $code = $response->getStatusCode();
        if ($code !== 200) {
            throw new ApiException(
                sprintf(
                    'API returned unexpected code %s: response: %s',
                    $code,
                    $response->getBody()->getContents()
                ),
                $code,
            );
        }

        $data = json_decode($response->getBody()->getContents(), true);
        if ($data === null) {
            throw new ApiException(
                sprintf(
                    'API returned invalid JSON: %s',
                    $response->getBody()->getContents(),
                )
            );
        }

        return $data;
    }
}

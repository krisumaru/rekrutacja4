<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Http;

use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use rekrutacja4\RestClient\Exception\ApiException;
use rekrutacja4\RestClient\Http\Transport\GuzzleTransport;

readonly class Client implements ClientInterface
{
    public function __construct(
        private TransportInterface $transport,
    ) {
    }

    public static function create(): self
    {
        $baseUrl = getenv('REKRUTACJA4_API_BASE_URI');
        if (empty($baseUrl)) {
            throw new InvalidArgumentException('Missing REKRUTACJA4_API_BASE_URI environment variable');
        }
        $apiAuthUser = getenv('REKRUTACJA4_API_AUTH_USER');
        $apiAuthPassword = getenv('REKRUTACJA4_API_AUTH_PASSWORD');
        $authConfig = [];
        if (!empty($apiAuthUser) && !empty($apiAuthPassword)) {
            $authConfig = [$apiAuthUser, $apiAuthPassword];
        }
        $apiTimeout = getenv('REKRUTACJA4_API_TIMEOUT');

        return new self(
            new GuzzleTransport(
                new \GuzzleHttp\Client([
                    'base_uri' => $baseUrl,
                    'auth' => $authConfig,
                    'timeout' => $apiTimeout,
                    'http_errors' => false,
                ])
            ),
        );
    }

    /**
     * @param array<string, string> $headers
     */
    public function get(string $path, array $headers = []): ResponseInterface
    {
        return $this->request('GET', $path, null, $headers);
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     */
    public function post(string $path, array $body, array $headers = []): ResponseInterface
    {
        return $this->request('POST', $path, $body, $headers);
    }

    /**
     * @param array<string, mixed> $headers
     * @param array<string, mixed>|null $body
     */
    protected function request(string $method, string $path, ?array $body = null, array $headers = []): ResponseInterface
    {
        $uri = trim($path, '/');
        $payload = $body !== null ? json_encode($body) : null;
        if ($payload === false) {
            throw new InvalidArgumentException('Failed to encode request body as JSON');
        }
        $request = new Request($method, $uri, $headers, $payload);

        return $this->transport->sendRequest($request);
    }
}

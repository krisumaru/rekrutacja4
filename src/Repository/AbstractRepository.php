<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Repository;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Request;
use rekrutacja4\RestClient\Exception\ApiException;
use rekrutacja4\RestClient\Exception\ValidationException;
use rekrutacja4\RestClient\Http\ClientInterface;

abstract class AbstractRepository
{
    protected ClientInterface $http;
    protected string $baseUri;
    protected array $defaultHeaders = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    public function __construct(ClientInterface $http, string $baseUri)
    {
        $this->http = $http;
        $this->baseUri = rtrim($baseUri, '/');
    }

    protected function request(string $method, string $path, ?array $body = null, array $headers = []): array
    {
        $uri = $this->baseUri . '/' . ltrim($path, '/');
        $payload = $body !== null ? json_encode($body) : null;
        $reqHeaders = array_merge($this->defaultHeaders, $headers);
        $request = new Request($method, $uri, $reqHeaders, $payload);

        $response = $this->http->sendRequest($request);

        return $this->handleResponse($response);
    }

    protected function handleResponse(ResponseInterface $response): array
    {
        $code = $response->getStatusCode();
        $body = (string)$response->getBody();
        $data = $body === '' ? [] : json_decode($body, true);

        if (!is_array($data)) {
            if ($code < 200 || $code >= 300) {
                throw new ApiException(sprintf('API error: empty or invalid JSON (status %d)', $code), $code);
            }
            return [];
        }

        // standard wrapper: { version, success, data, error }
        if (isset($data['success']) && $data['success'] === false) {
            $error = $data['error'] ?? [];
            $reason = $error['reason_code'] ?? '';
            $messages = $error['messages'] ?? [];
            $msg = is_array($messages) ? implode('; ', $messages) : ($error['message'] ?? $body);
            if ($reason === 'INVALID_DATA_FOR_OBJECT') {
                throw new ValidationException($msg ?: 'Validation failed', (array)$messages, $code);
            }
            throw new ApiException($msg ?: 'API returned error', $code);
        }

        // success wrapper: prefer data content
        if (isset($data['data'])) {
            return (array)$data['data'];
        }

        // fallback - maybe API returns object directly (producer)
        return $data;
    }
}

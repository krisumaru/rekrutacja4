<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Repository;

use Psr\Http\Message\ResponseInterface;
use rekrutacja4\RestClient\Exception\ApiException;
use rekrutacja4\RestClient\Exception\BadRequestException;
use rekrutacja4\RestClient\Exception\ConflictException;
use rekrutacja4\RestClient\Exception\ValidationException;
use rekrutacja4\RestClient\Http\ClientInterface;

abstract class AbstractRepository
{
    protected string $baseUri;

    public function __construct(
        private readonly ClientInterface $client
    ) {
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function post(string $path, array $data): array
    {
        $response = $this->client->post($path, $data);
        return $this->handleJsonResponse($response);
    }

    /**
     * @return array<string, mixed>
     */
    protected function handleJsonResponse(ResponseInterface $response): array
    {
        $code = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        if ($code < 200 || $code >= 500) {
            throw new ApiException(
                sprintf(
                    'API returned unexpected code %s: response: %s',
                    $code,
                    $body
                ),
                $code,
            );
        }

        $data = json_decode($body, true);
        if ($data === null) {
            throw new ApiException(
                sprintf(
                    'API returned invalid JSON: %s',
                    $body,
                )
            );
        }

        if ($code === 400) {
            $error = $data['error'] ?? [];
            $reason = $error['reason_code'] ?? '';
            $messages = $error['messages'] ?? [];
            if ($reason === 'INVALID_DATA_FOR_OBJECT') {
                throw new ValidationException('Validation failed', $messages, $code);
            }
            throw new ApiException(
                sprintf('API returned error: %s', $body),
                $code,
            );
        }

        if ($code === 409) {
            throw new ConflictException(
                sprintf('API Conflict: %s', $body),
                $code
            );
        }

        if ($code >= 400) {
            throw new BadRequestException(
                sprintf('Bad request with code %s: %s', $code, $body),
                $code
            );
        }

        return $data;
    }
}

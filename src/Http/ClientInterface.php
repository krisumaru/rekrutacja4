<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Http;

use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * @param array<string, string> $headers
     */
    public function get(string $path, array $headers = []): ResponseInterface;

    /**
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     */
    public function post(string $path, array $body, array $headers = []): ResponseInterface;
}

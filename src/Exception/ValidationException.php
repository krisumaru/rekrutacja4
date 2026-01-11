<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Exception;

class ValidationException extends ApiException
{
    private array $messages;

    public function __construct(string $message = '', array $messages = [], int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->messages = $messages;
    }

    public function getValidationMessages(): array
    {
        return $this->messages;
    }
}

<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Exception;

use Throwable;

final class ValidationException extends ApiException
{
    /**
     * @param array<string> $validationErrors
     */
    public function __construct(
        string $message = '',
        readonly private array $validationErrors = [],
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array<string>
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}

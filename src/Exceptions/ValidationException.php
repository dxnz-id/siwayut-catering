<?php
declare(strict_types=1);
// File: src/Exceptions/ValidationException.php

namespace App\Exceptions;

class ValidationException extends AppException {
    public function __construct(private array $errors, string $message = 'Validation failed') {
        parent::__construct($message);
    }

    // CONTRACT: getErrors() MUST return the promoted $errors property.
    public function getErrors(): array {
        return $this->errors;
    }
}

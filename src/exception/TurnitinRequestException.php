<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/28 16:20
 */

namespace NzhC\Turnitin\exception;

use Throwable;

class TurnitinRequestException extends \RuntimeException
{
    private array $errors = [];

    private ?int $httpCode;

    public function __construct(?string $message = "",array $errors = [], int $code = 0,?Throwable $previous = null,?int $httpCode=null)
    {
        $this->errors = $errors;
        $this->httpCode = $httpCode;
        parent::__construct($message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getHttpCode(): ?int
    {
        return $this->httpCode;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/30 13:38
 */

namespace NzhC\Turnitin\exception;

class TurnitinRuntimeException extends \RuntimeException
{
    public function __construct(?string $message = "", int $code = 0,?Throwable $previous = null)
    {
        parent::__construct($message);
    }
}
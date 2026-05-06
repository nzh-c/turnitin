<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/28 16:27
 */

namespace NzhC\Turnitin\exception;

class TurnitinParameterException extends \RuntimeException
{
    public function __construct(?string $message = "", int $code = 0,?Throwable $previous = null)
    {
        parent::__construct($message);
    }
}
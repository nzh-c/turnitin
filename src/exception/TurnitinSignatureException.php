<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/30 11:40
 */

namespace NzhC\Turnitin\exception;

class TurnitinSignatureException extends \RuntimeException
{
    public function __construct(?string $message = "", int $code = 0,?Throwable $previous = null)
    {
        parent::__construct($message);
    }
}
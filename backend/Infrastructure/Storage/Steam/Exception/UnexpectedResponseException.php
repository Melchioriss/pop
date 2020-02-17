<?php

namespace PlayOrPay\Infrastructure\Storage\Steam\Exception;

use Exception;

class UnexpectedResponseException extends Exception
{
    public static function becauseFieldDoentExists(string $field)
    {
        return new self(sprintf("Expected '%s' field wasn't found in the response", $field));
    }
}

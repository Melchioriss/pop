<?php

namespace PlayOrPay\Infrastructure\Storage\Steam\Exception;

use Exception;

class UnexpectedResponseException extends Exception
{
    public static function becauseFieldDoesntExist(string $field): self
    {
        return new self(sprintf("Expected '%s' field wasn't found in the response", $field));
    }
}

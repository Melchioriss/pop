<?php

namespace PlayOrPay\Infrastructure\Storage\Steam\Exception;

use Exception;

class UnexpectedResponseException extends Exception
{
    /**
     * @param string $field
     * @param array<string, mixed> $query
     *
     * @return UnexpectedResponseException
     */
    public static function becauseFieldDoesntExist(string $field, array $query): self
    {
        return new self(sprintf("Expected '%s' field wasn't found in the response for query '%s'", $field, http_build_query($query)));
    }
}

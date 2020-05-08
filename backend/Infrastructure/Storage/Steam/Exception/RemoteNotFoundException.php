<?php

namespace PlayOrPay\Infrastructure\Storage\Steam\Exception;

use Exception;

class RemoteNotFoundException extends Exception
{
    /**
     * @param string $class
     * @param array<string, string> $query
     *
     * @return RemoteNotFoundException
     */
    public static function forQuery(string $class, array $query): self
    {
        return new self(sprintf("Request '%s' hasn't found any '%s'", json_encode($query), $class));
    }
}

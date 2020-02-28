<?php

namespace PlayOrPay\Infrastructure\Storage\Steam\Exception;

use Exception;

class RemoteNotFoundException extends Exception
{
    public static function forQuery(string $class, array $query)
    {
        return new self(sprintf("Request '%s' hasn't found any '%s'", json_encode($query), $class));
    }
}

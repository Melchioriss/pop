<?php

namespace PlayOrPay\Domain\Exception;

use Exception;

class NotFoundException extends Exception
{
    public static function forObject(string $class, string $identifier)
    {
        return new self(sprintf("Object of '%s' class and identity '%s' wasn't found", $class, $identifier));
    }
}

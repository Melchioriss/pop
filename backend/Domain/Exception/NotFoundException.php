<?php

namespace PlayOrPay\Domain\Exception;

use Exception;

class NotFoundException extends Exception
{
    public static function forObject(string $class, string $identifier): self
    {
        return new self(sprintf("Object of '%s' class and identity '%s' wasn't found", $class, $identifier));
    }

    /**
     * @param string $class
     * @param array<string, int|string|null> $query
     *
     * @return NotFoundException
     */
    public static function forQuery(string $class, array $query): self
    {
        return new self(sprintf("Object of '%s' class wasn't found with query %s", $class, var_export($query, true)));
    }
}

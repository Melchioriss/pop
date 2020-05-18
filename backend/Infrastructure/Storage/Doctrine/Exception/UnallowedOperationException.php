<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Exception;

use Exception;

class UnallowedOperationException extends Exception
{
    public static function becauseSavingIsAvailableOnlyOnAggregate(string $entityClass): self
    {
        return new self(sprintf("You must call save() only on aggregates, but '%s' ins't an aggregate", $entityClass));
    }
}

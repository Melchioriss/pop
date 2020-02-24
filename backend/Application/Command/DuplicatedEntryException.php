<?php

namespace PlayOrPay\Application\Command;

use Exception;

class DuplicatedEntryException extends Exception
{
    public static function collectionAlreadyHas(string $collectionName, string $memberName): self
    {
        return new self(sprintf("Collection '%s' already has member '%s'", $collectionName, $memberName));
    }
}

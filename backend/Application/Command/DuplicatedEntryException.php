<?php

namespace PlayOrPay\Application\Command;

use Exception;

class DuplicatedEntryException extends Exception
{
    public static function collectionAlreadyHas(string $collectionName, string $entryName): self
    {
        return new self(sprintf("Collection '%s' already has member '%s'", $collectionName, $entryName));
    }
}

<?php

namespace Insideone\Package\Collection;

use Exception;

class UnidentifiableObjectException extends Exception
{
    public static function create(object $object): self
    {
        return new self(sprintf("%s must implement %s, but it doesn't", get_class($object), Identifiable::class));
    }
}

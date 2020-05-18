<?php

namespace PlayOrPay\Application\Command\User\User\AddRoles;

use Exception;

class UnexpectedRoleException extends Exception
{
    public static function alreadyExists(string $role): self
    {
        return new self(sprintf('User already treated as %s', $role));
    }
}

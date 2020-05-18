<?php

namespace PlayOrPay\Domain\User\Exception;

use Exception;
use PlayOrPay\Domain\Steam\Group;
use PlayOrPay\Domain\User\User;

class UserAlreadyInGroupException extends Exception
{
    public static function create(User $user, Group $group): self
    {
        return new self(sprintf("Group '%s' already has member '%s'", $group->getName(), $user->getProfileName()));
    }
}

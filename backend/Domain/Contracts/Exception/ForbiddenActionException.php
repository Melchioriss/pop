<?php

namespace PlayOrPay\Domain\Contracts\Exception;

use DomainException;
use PlayOrPay\Domain\User\User;

class ForbiddenActionException extends DomainException
{
    public static function forThisActor(User $actor, string $action): self
    {
        return new self(sprintf("Action '%s' is forbidden for actor '%s'", $action, $actor->getUsername()));
    }
}

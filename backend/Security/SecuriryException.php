<?php

namespace PlayOrPay\Security;

use Exception;
use PlayOrPay\Domain\Steam\Group;
use PlayOrPay\Domain\User\User;

class SecuriryException extends Exception
{
    public static function becauseActorIsntAllowedToDoIt(User $actor, string $action): self
    {
        return new self(sprintf("Actor '%s' isn't allowed to do '%s'", $actor->getProfileName(), $action));
    }

    public static function becauseThereIsNoActor(): self
    {
        return new self(sprintf('Action was forbidden because there is no actor, but we need them'));
    }

    public static function becauseOfUnexpectedActor(User $actor, User $user): self
    {
        return new self(sprintf("The action is allowed for '%s', not for '%s'", $user->getProfileName(), $actor->getProfileName()));
    }

    public static function becauseYouMustImplementSecurityHandler(string $action): self
    {
        return new self(sprintf("Security handler for action '%s' isn't found. You must implement security handler for each REST command to keep application safe", $action));
    }

    public static function becauseUserDontBelongToGroup(User $user, Group $group): self
    {
        return new self(sprintf("User '%s' must be in '%s' group to do this", $user->getProfileName(), $group->getName()));
    }

    /**
     * @param User[] $users
     *
     * @return SecuriryException
     */
    public static function becauseItsAllowedOnlyToCertainUsers(array $users): self
    {
        $userNames = array_map(function (User $user) {
            return $user->getProfileName();
        }, $users);

        return new self(sprintf('This is allowed only for certain users: %s', implode(', ', $userNames)));
    }
}

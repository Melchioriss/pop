<?php

namespace PlayOrPay\Security;

use Doctrine\Common\Collections\ArrayCollection;
use PlayOrPay\Domain\Steam\Group;
use PlayOrPay\Domain\User\User;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;

abstract class CommonSecurityHandler implements SecurityHandlerInterface
{
    /** @var ActorFinder */
    private $actorFinder;

    public function __construct(ActorFinder $actorFinder)
    {
        $this->actorFinder = $actorFinder;
    }

    /**
     * @throws SecuriryException
     *
     * @return User
     */
    public function getActor(): User
    {
        $actor = $this->actorFinder->findActor();
        if (!$actor) {
            throw SecuriryException::becauseThereIsNoActor();
        }

        return $actor;
    }

    public function isAdmin(): bool
    {
        $actor = $this->actorFinder->findActor();
        if (!$actor) {
            return false;
        }

        return $actor->isAdmin();
    }

    /**
     * @throws SecuriryException
     */
    public function assertAdmin(): void
    {
        if ($this->isAdmin()) {
            return;
        }

        throw new SecuriryException('You must be admin to do that');
    }

    /**
     * @param User $user
     *
     * @throws SecuriryException
     */
    public function assertActor(User $user): void
    {
        $actor = $this->getActor();
        if ($actor === $user) {
            return;
        }

        throw SecuriryException::becauseOfUnexpectedActor($actor, $user);
    }

    /**
     * @param Group $group
     *
     * @throws SecuriryException
     */
    public function assertBeingInGroup(Group $group): void
    {
        $actor = $this->getActor();
        if ($group->hasUser($actor)) {
            return;
        }

        throw SecuriryException::becauseUserDontBelongToGroup($actor, $group);
    }

    /**
     * @param User[] $users
     * @param bool   $acceptAdmin
     *
     * @throws SecuriryException
     */
    public function assertBeingOneOf(array $users, bool $acceptAdmin = false): void
    {
        $actor = $this->getActor();
        if ($acceptAdmin && $actor->isAdmin()) {
            return;
        }

        if (!(new ArrayCollection($users))->contains($actor)) {
            throw SecuriryException::becauseItsAllowedOnlyToCertainUsers($users);
        }
    }
}

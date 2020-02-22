<?php

namespace PlayOrPay\Infrastructure\Storage\User;

use DomainException;
use PlayOrPay\Domain\User\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ActorFinder implements ActorFinderInterface
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function findActor(): ?User
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return null;
        }

        $user = $token->getUser();
        if ($user instanceof User) {
            return $user;
        }

        return null;
    }

    /**
     * @throws DomainException
     *
     * @return User
     */
    public function getActor(): User
    {
        $actor = $this->findActor();
        if ($actor) {
            return $actor;
        }

        throw new DomainException('You must be logged in to proceed');
    }
}

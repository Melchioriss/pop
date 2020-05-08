<?php

namespace PlayOrPay\Security\User\User;

use Doctrine\ORM\EntityNotFoundException;
use PlayOrPay\Application\Command\User\User\Deactivate\DeactivateUserCommand;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class DeactivateUserSecurityHandler extends CommonSecurityHandler
{
    /** @var UserRepository */
    private $userRepo;

    public function __construct(ActorFinder $actorFinder, UserRepository $userRepo)
    {
        parent::__construct($actorFinder);
        $this->userRepo = $userRepo;
    }

    /**
     * @param DeactivateUserCommand $command
     *
     * @throws EntityNotFoundException
     * @throws SecuriryException
     */
    public function __invoke(DeactivateUserCommand $command): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $user = $this->userRepo->get($command->getSteamId());
        $this->assertActor($user);
    }
}

<?php

namespace PlayOrPay\Application\Command\User\User\Deactivate;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

class DeactivateUserHandler implements CommandHandlerInterface
{
    /** @var UserRepository */
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @param DeactivateUserCommand $command
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function __invoke(DeactivateUserCommand $command)
    {
        $user = $this->userRepo->find($command->getSteamId());
        $user->deactivate();
        $this->userRepo->save($user);
    }
}

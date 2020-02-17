<?php

namespace PlayOrPay\Application\Command\User\User\Activate;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

class ActivateUserHandler implements CommandHandlerInterface
{
    /** @var UserRepository */
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @param ActivateUserCommand $command
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function __invoke(ActivateUserCommand $command)
    {
        $user = $this->userRepo->find($command->getSteamId());
        $user->activate();
        $this->userRepo->save($user);
    }
}

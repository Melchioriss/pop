<?php

namespace PlayOrPay\Application\Command\User\User\SetBlaeoName;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

class SetUserBlaeoNameHandler implements CommandHandlerInterface
{
    /** @var UserRepository */
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @param SetUserBlaeoNameCommand $command
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function __invoke(SetUserBlaeoNameCommand $command)
    {
        $user = $this->userRepo->find($command->getSteamId());
        $user->setBlaeoName($command->getBlaeoName());
        $this->userRepo->save($user);
    }
}

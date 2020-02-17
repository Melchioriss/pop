<?php

namespace PlayOrPay\Application\Command\User\User\SetExtraRules;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

class SetUserExtraRulesHandler implements CommandHandlerInterface
{
    /** @var UserRepository */
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @param SetUserExtraRulesCommand $command
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function __invoke(SetUserExtraRulesCommand $command)
    {
        $user = $this->userRepo->find($command->getSteamId());
        $user->setExtraRules($command->getExtraRules());
        $this->userRepo->save($user);
    }
}

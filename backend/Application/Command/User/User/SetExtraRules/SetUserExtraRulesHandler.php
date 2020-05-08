<?php

namespace PlayOrPay\Application\Command\User\User\SetExtraRules;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
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
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     * @throws EntityNotFoundException
     */
    public function __invoke(SetUserExtraRulesCommand $command): void
    {
        $user = $this->userRepo->get($command->getSteamId());
        $user->setExtraRules($command->getExtraRules());
        $this->userRepo->save($user);
    }
}

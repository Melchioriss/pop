<?php

namespace PlayOrPay\Application\Command\User\User\AddGroups;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Application\Command\DuplicatedEntryException;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Steam\GroupRepository;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

class AddUserGroupsHandler implements CommandHandlerInterface
{
    /** @var UserRepository */
    private $userRepo;

    /** @var GroupRepository */
    private $groupRepo;

    public function __construct(UserRepository $userRepo, GroupRepository $groupRepo)
    {
        $this->userRepo = $userRepo;
        $this->groupRepo = $groupRepo;
    }

    /**
     * @param AddUserGroupsCommand $command
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     * @throws DuplicatedEntryException
     */
    public function __invoke(AddUserGroupsCommand $command)
    {
        $user = $this->userRepo->get($command->steamId);

        $groups = $this->groupRepo->findByCodes($command->groupNames);

        $user->addGroups($groups);

        $this->userRepo->save($user);
    }
}

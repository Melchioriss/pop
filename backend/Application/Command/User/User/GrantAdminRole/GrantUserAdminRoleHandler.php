<?php

namespace PlayOrPay\Application\Command\User\User\GrantAdminRole;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Domain\Role\Role;
use PlayOrPay\Domain\Role\RoleName;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\User\RoleRepository;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

class GrantUserAdminRoleHandler implements CommandHandlerInterface
{
    /** @var UserRepository */
    private $userRepo;

    /** @var RoleRepository */
    private $roleRepo;

    public function __construct(UserRepository $userRepo, RoleRepository $roleRepo)
    {
        $this->userRepo = $userRepo;
        $this->roleRepo = $roleRepo;
    }

    /**
     * @param GrantUserAdminRoleCommand $command
     *
     * @throws NotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     * @throws EntityNotFoundException
     */
    public function __invoke(GrantUserAdminRoleCommand $command): void
    {
        $steamId = $command->getSteamId();
        $user = $this->userRepo->get($steamId);
        $adminRole = $this->roleRepo->find(RoleName::ADMIN);
        if (!$adminRole) {
            throw NotFoundException::forObject(Role::class, RoleName::ADMIN);
        }

        $user->addRole($adminRole);
        $this->userRepo->save($user);
    }
}

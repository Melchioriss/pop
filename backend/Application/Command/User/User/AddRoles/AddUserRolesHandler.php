<?php

namespace PlayOrPay\Application\Command\User\User\AddRoles;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\User\RoleRepository;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

class AddUserRolesHandler implements CommandHandlerInterface
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
     * @param AddUserRolesCommand $command
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnexpectedRoleException
     * @throws UnallowedOperationException
     */
    public function __invoke(AddUserRolesCommand $command): void
    {
        $user = $this->userRepo->get($command->getSteamId());
        $newRoles = $command->getRoleNames();
        foreach ($newRoles as $newRole) {
            if ($user->hasRole($newRole)) {
                throw UnexpectedRoleException::alreadyExists($newRole);
            }
        }

        $roles = $this->roleRepo->findBy([
            'name' => $command->getRoleNames(),
        ]);

        $user->addRoles(...$roles);
        $this->userRepo->save($user);
    }
}

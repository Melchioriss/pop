<?php

namespace PlayOrPay\Application\Command\User\User\AddRoles;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Domain\Role\RoleName;
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
     * @throws NotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnexpectedRoleException
     */
    public function __invoke(AddUserRolesCommand $command)
    {
        $user = $this->userRepo->find($command->getSteamId());
        if (!$user) {
            throw new NotFoundException;
        }

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

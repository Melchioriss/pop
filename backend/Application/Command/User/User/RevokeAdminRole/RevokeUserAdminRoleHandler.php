<?php

namespace PlayOrPay\Application\Command\User\User\RevokeAdminRole;

use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Domain\Role\RoleName;
use PlayOrPay\Domain\User\User;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

class RevokeUserAdminRoleHandler implements CommandHandlerInterface
{
    /** @var UserRepository */
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @param RevokeUserAdminRoleCommand $command
     * @throws NotFoundException
     */
    public function __invoke(RevokeUserAdminRoleCommand $command)
    {
        $steamId = $command->getSteamId();
        $user = $this->userRepo->find($steamId);
        if (!$user) {
            throw NotFoundException::forObject(User::class, (string)$steamId);
        }

        $user->removeRole(new RoleName(RoleName::ADMIN));
        $this->userRepo->save($user);
    }
}

<?php

namespace PlayOrPay\Application\Command\User\User\RevokeAdminRole;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Role\RoleName;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
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
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     */
    public function __invoke(RevokeUserAdminRoleCommand $command): void
    {
        $steamId = $command->getSteamId();
        $user = $this->userRepo->get($steamId);
        $user->removeRole(new RoleName(RoleName::ADMIN));
        $this->userRepo->save($user);
    }
}

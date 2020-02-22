<?php

namespace PlayOrPay\Application\Command\User\User\AddRoles;

use Assert\Assert;
use PlayOrPay\Domain\Role\RoleName;
use PlayOrPay\Domain\Steam\SteamId;

class AddUserRolesCommand
{
    /** @var SteamId */
    private $steamId;

    /** @var RoleName[] */
    private $roleNames;

    /**
     * @param int $steamId
     * @param string[] $roles
     */
    public function __construct(int $steamId, array $roles)
    {
        Assert::thatAll($roles)->string();
        Assert::that($roles)->minCount(1);

        $this->steamId = new SteamId($steamId);
        $this->roleNames = array_map(function (string $role) {
            return new RoleName($role);
        }, $roles);
    }

    public function getSteamId(): SteamId
    {
        return $this->steamId;
    }

    /**
     * @return RoleName[]
     */
    public function getRoleNames(): array
    {
        return $this->roleNames;
    }
}

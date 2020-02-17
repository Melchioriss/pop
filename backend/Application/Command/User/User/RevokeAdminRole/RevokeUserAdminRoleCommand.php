<?php

namespace PlayOrPay\Application\Command\User\User\RevokeAdminRole;

use PlayOrPay\Domain\Steam\SteamId;

class RevokeUserAdminRoleCommand
{
    /** @var SteamId */
    private $steamId;

    public function __construct(int $steamId)
    {
        $this->steamId = new SteamId($steamId);
    }

    public function getSteamId(): SteamId
    {
        return $this->steamId;
    }
}

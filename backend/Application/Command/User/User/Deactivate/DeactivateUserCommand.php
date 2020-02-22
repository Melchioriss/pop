<?php

namespace PlayOrPay\Application\Command\User\User\Deactivate;

use PlayOrPay\Domain\Steam\SteamId;

class DeactivateUserCommand
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

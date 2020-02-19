<?php

namespace PlayOrPay\Application\Command\User\User\Activate;

use PlayOrPay\Domain\Steam\SteamId;

class ActivateUserCommand
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

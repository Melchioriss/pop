<?php

namespace PlayOrPay\Application\Query\Steam;

use Assert\Assert;
use PlayOrPay\Domain\Steam\SteamId;

class UserStatsQuery
{
    /** @var SteamId */
    public $steamId;

    /** @var int */
    public $appId;

    public function __construct(int $steamId, int $appId)
    {
        Assert::that($appId)->greaterThan(0);

        $this->steamId = new SteamId($steamId);
        $this->appId = $appId;
    }
}

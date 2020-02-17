<?php

namespace PlayOrPay\Domain\Steam;

use Assert\Assert;

class RecentlyPlayedGame
{
    /** @var int */
    public $appId;

    /** @var int */
    public $playtimeForever;

    public function __construct(int $appId, int $playtimeForever)
    {
        Assert::lazy()
            ->that($appId)->greaterThan(0)
            ->that($playtimeForever)->greaterOrEqualThan(0)
            ->verifyNow();

        $this->appId = $appId;
        $this->playtimeForever = $playtimeForever;
    }
}

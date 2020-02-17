<?php

namespace PlayOrPay\Domain\Steam;

use Assert\Assert;

class PlayerAchievement
{
    /** @var int */
    public $appId;

    /** @var bool */
    public $achieved;

    public function __construct(int $appId, bool $achieved)
    {
        Assert::that($appId)->greaterThan(0);

        $this->appId = $appId;
        $this->achieved = $achieved;
    }
}

<?php

namespace PlayOrPay\Domain\Steam;

class OwnedGame
{
    /** @var string|null */
    public $name;

    /** @var int */
    public $appId;

    /** @var int */
    public $playtimeForever;

    public function __construct(?string $name, int $appId, int $playtimeForever)
    {
        $this->name = $name;
        $this->appId = $appId;
        $this->playtimeForever = $playtimeForever;
    }
}

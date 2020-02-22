<?php

namespace PlayOrPay\Application\Command\User\User\SetExtraRules;

use PlayOrPay\Domain\Steam\SteamId;

class SetUserExtraRulesCommand
{
    /** @var SteamId */
    private $steamId;

    /** @var string */
    private $extraRules;

    public function __construct(int $steamId, string $extraRules)
    {
        $this->steamId = new SteamId($steamId);
        $this->extraRules = $extraRules;
    }

    public function getSteamId(): SteamId
    {
        return $this->steamId;
    }

    public function getExtraRules(): string
    {
        return $this->extraRules;
    }
}

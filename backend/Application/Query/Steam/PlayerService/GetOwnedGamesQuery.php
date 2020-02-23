<?php

namespace PlayOrPay\Application\Query\Steam\PlayerService;

use Assert\Assert;
use PlayOrPay\Domain\Steam\SteamId;

class GetOwnedGamesQuery
{
    /** @var SteamId */
    private $steamId;

    /** @var bool */
    private $appInfoIncluded;

    /** @var bool */
    private $playedFreeGamesIncluded;

    /** @var string[] */
    private $appIdsFilter;

    public function __construct(int $steamId)
    {
        $this->steamId = new SteamId($steamId);
    }

    public function includeAppInfo(): self
    {
        $this->appInfoIncluded = true;

        return $this;
    }

    public function includePlayedFreeGames(): self
    {
        $this->playedFreeGamesIncluded = true;

        return $this;
    }

    /**
     * @param string[] $apps
     *
     * @return self
     */
    public function forApps(array $apps): self
    {
        Assert::thatAll($apps)->string();
        $this->appIdsFilter = $apps;

        return $this;
    }

    public function getSteamId(): SteamId
    {
        return $this->steamId;
    }

    public function appInfoIncluded(): bool
    {
        return $this->appInfoIncluded;
    }

    public function playedFreeGamesIncluded(): bool
    {
        return $this->playedFreeGamesIncluded;
    }

    /**
     * @return string[]
     */
    public function getAppIdsFilter(): ?array
    {
        return $this->appIdsFilter;
    }
}

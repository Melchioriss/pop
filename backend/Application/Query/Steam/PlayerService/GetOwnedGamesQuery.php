<?php

namespace PlayOrPay\Application\Query\Steam\PlayerService;

use PlayOrPay\Domain\Steam\SteamId;

class GetOwnedGamesQuery
{
    /** @var SteamId */
    private $steamId;

    /** @var bool */
    private $appInfoIncluded;

    /** @var bool */
    private $playedFreeGamesIncluded;

    /** @var int[] */
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

    public function forApps(array $apps): self
    {
        $this->appIdsFilter = $apps;
        return $this;
    }

    /**
     * @return SteamId
     */
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
     * @return int[]
     */
    public function getAppIdsFilter(): ?array
    {
        return $this->appIdsFilter;
    }
}

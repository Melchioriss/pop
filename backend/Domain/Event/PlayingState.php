<?php

namespace PlayOrPay\Domain\Event;

use Assert\Assert;

class PlayingState
{
    /** @var int|null */
    private $playtime;

    /** @var int|null */
    private $achievements;

    public function __construct(?int $playtime = null, ?int $achievements = null)
    {
        Assert::thatAll([$playtime, $achievements])->nullOr()->greaterOrEqualThan(0);

        $this->playtime = $playtime;
        $this->achievements = $achievements;
    }

    public function getPlaytime(): ?int
    {
        return $this->playtime;
    }

    public function getAchievements(): ?int
    {
        return $this->achievements;
    }

    public function updatePlaytime(int $playtime): self
    {
        Assert::that($playtime)->greaterOrEqualThan(0);
        $this->playtime = $playtime;

        return $this;
    }

    public function updateAchievements(int $achievements): self
    {
        Assert::that($achievements)->greaterOrEqualThan(0);
        $this->achievements = $achievements;

        return $this;
    }

    public function clear(): void
    {
        $this->updatePlaytime(0);
        $this->updateAchievements(0);
    }
}

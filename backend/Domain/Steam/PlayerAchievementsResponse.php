<?php

namespace PlayOrPay\Domain\Steam;

use Assert\Assert;
use Countable;

class PlayerAchievementsResponse implements Countable
{
    /** @var PlayerAchievement[] */
    private $achievements;

    /**
     * @param PlayerAchievement[] $achievements
     */
    public function __construct(array $achievements)
    {
        Assert::thatAll($achievements)->isInstanceOf(PlayerAchievement::class);

        $this->achievements = $achievements;
    }

    public function countAchieved(): int
    {
        $achieved = 0;
        foreach ($this->achievements as $achievement) {
            $achieved += (int) $achievement->achieved;
        }

        return $achieved;
    }

    public function count(): int
    {
        return count($this->achievements);
    }
}

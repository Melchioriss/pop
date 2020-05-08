<?php

namespace PlayOrPay\Domain\Event;

use Assert\Assert;
use PlayOrPay\Infrastructure\Storage\Event\EventRewardRepository;

class SuitablePickRewardFinder
{
    /** @var EventReward[] */
    private $rewards;

    const REWARD_MAP = [
        EventPickPlayedStatus::UNFINISHED => null,
        EventPickPlayedStatus::NOT_PLAYED => null,
        EventPickPlayedStatus::ABANDONED  => null,
        EventPickPlayedStatus::BEATEN     => [
            EventPickType::SHORT     => RewardReason::SHORT_GAME_BEATEN,
            EventPickType::MEDIUM    => RewardReason::MEDIUM_GAME_BEATEN,
            EventPickType::LONG      => RewardReason::LONG_GAME_BEATEN,
            EventPickType::VERY_LONG => RewardReason::VERY_LONG_GAME_BEATEN,
        ],
        EventPickPlayedStatus::COMPLETED => RewardReason::GAME_COMPLETED,
    ];

    public function __construct(EventRewardRepository $rewardRepo)
    {
        foreach ($rewardRepo->findAll() as $reward) {
            $this->rewards[(int) (string) $reward->getReason()] = $reward;
        }
    }

    /**
     * @param EventPickPlayedStatus $status
     * @param EventPickType $pickType
     *
     * @return EventReward[]
     */
    public function __invoke(EventPickPlayedStatus $status, EventPickType $pickType): array
    {
        Assert::that(self::REWARD_MAP)->keyExists($status->__default);

        $possibleReasons = self::REWARD_MAP[$status->__default];
        if ($possibleReasons === null) {
            return [];
        }

        $reason = $possibleReasons;
        if (is_array($possibleReasons)) {
            Assert::that($possibleReasons)->keyExists($pickType->__default);
            $reason = $possibleReasons[$pickType->__default];
        }

        Assert::that($this->rewards)->keyExists($reason);

        $rewards = [$this->rewards[$reason]];

        if ($status->equalTo(EventPickPlayedStatus::COMPLETED)) {
            array_push(
                $rewards,
                ...$this->__invoke(new EventPickPlayedStatus(EventPickPlayedStatus::BEATEN), $pickType)
            );
        }

        return $rewards;
    }
}

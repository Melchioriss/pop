<?php

namespace PlayOrPay\Domain\Event;

use Assert\Assert;
use DomainException;
use Ramsey\Uuid\UuidInterface;

class EventEarnedReward
{
    /** @var UuidInterface */
    private $uuid;

    /** @var EventParticipant */
    private $participant;

    /** @var EventReward */
    private $reward;

    /** @var int */
    private $value;

    public function __construct(UuidInterface $uuid, EventParticipant $participant, EventReward $reward, ?int $value)
    {
        $this->uuid = $uuid;
        $this->participant = $participant;
        $this->reward = $reward;

        $defaultValue = $this->reward->getValue();
        if ($value === null && $defaultValue === null) {
            throw new DomainException('Either passed value or achievement value must be not null');
        }

        $this->value = $value === null ? $defaultValue : $value;
    }

    public function getReason(): RewardReason
    {
        return $this->reward->getReason();
    }

    public function updateValue(int $value)
    {
        Assert::that($value)->greaterOrEqualThan(1);
        $this->value = $value;
    }
}

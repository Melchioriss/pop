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

    /** @var EventPick|null */
    private $pick;

    /** @var EventReward */
    private $reward;

    /** @var int */
    private $value;

    public function __construct(UuidInterface $uuid, EventParticipant $participant, ?EventPick $pick, EventReward $reward, ?int $value)
    {
        $this->uuid = $uuid;
        $this->participant = $participant;
        $this->pick = $pick;
        $this->reward = $reward;

        $defaultValue = $this->reward->getValue();
        if ($value === null && $defaultValue === null) {
            throw new DomainException('Either passed value or reward value must be not null');
        }

        $this->value = $value === null ? $defaultValue : $value;
    }

    public function getReason(): RewardReason
    {
        return $this->reward->getReason();
    }

    public function updateValue(int $value): void
    {
        Assert::that($value)->greaterOrEqualThan(1);
        $this->value = $value;
    }

    public function isFor(RewardReason $reason, ?UuidInterface $pickUuid): bool
    {
        return $reason->equalTo($this->getReason()) && $this->isForPick($pickUuid);
    }

    public function isForPick(?UuidInterface $pickUuid): bool
    {
        if ($pickUuid) {
            return $this->pick ? $this->pick->getUuid()->equals($pickUuid) : false;
        }

        return $this->pick === null;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getParticipant(): EventParticipant
    {
        return $this->participant;
    }

    public function getPick(): ?EventPick
    {
        return $this->pick;
    }
}

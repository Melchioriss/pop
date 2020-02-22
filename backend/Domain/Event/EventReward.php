<?php

namespace PlayOrPay\Domain\Event;

use Assert\Assert;
use PlayOrPay\Domain\Contracts\Entity\AggregateInterface;
use PlayOrPay\Domain\Contracts\Entity\AggregateTrait;

class EventReward implements AggregateInterface
{
    use AggregateTrait;

    /** @var RewardReason */
    private $reason;

    /** @var int|null */
    private $value;

    public function __construct(RewardReason $reason, ?int $value)
    {
        Assert::that($value)->nullOr()->greaterOrEqualThan(1);

        $this->reason = $reason;
        $this->value = $value;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function getReason(): RewardReason
    {
        return $this->reason;
    }
}

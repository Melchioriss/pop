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

    /** @var float|null */
    private $value;

    public function __construct(RewardReason $reason, ?float $value)
    {
        Assert::that($value)->nullOr()->greaterOrEqualThan(1);

        $this->reason = $reason;
        $this->value = $value;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function getReason(): RewardReason
    {
        return $this->reason;
    }
}

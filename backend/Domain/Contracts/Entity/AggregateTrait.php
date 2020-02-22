<?php

namespace PlayOrPay\Domain\Contracts\Entity;

trait AggregateTrait
{
    /** @var object[] */
    private $events = [];

    private function addDomainEvent(object $event): self
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * @return object[]
     */
    public function popDomainEvents(): array
    {
        $collected = $this->events;
        $this->events = [];

        return $collected;
    }
}

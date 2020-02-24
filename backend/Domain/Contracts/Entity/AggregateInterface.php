<?php

namespace PlayOrPay\Domain\Contracts\Entity;

use PlayOrPay\Domain\Contracts\DomainEvent\DomainEventInterface;

interface AggregateInterface
{
    /**
     * @return DomainEventInterface[]
     */
    public function popDomainEvents(): array;
}

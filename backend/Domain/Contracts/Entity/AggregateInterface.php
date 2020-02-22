<?php

namespace PlayOrPay\Domain\Contracts\Entity;

interface AggregateInterface
{
    public function popDomainEvents(): array;
}

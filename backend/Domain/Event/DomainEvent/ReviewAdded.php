<?php

namespace PlayOrPay\Domain\Event\DomainEvent;

use PlayOrPay\Domain\Contracts\DomainEvent\DomainEventInterface;

class ReviewAdded implements DomainEventInterface
{
    public function serialize()
    {
        // TODO: Implement serialize() method.
    }

    public function unserialize($serialized)
    {
        // TODO
    }
}

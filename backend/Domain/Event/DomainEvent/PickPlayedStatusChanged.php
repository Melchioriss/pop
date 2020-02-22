<?php

namespace PlayOrPay\Domain\Event\DomainEvent;

use PlayOrPay\Domain\Contracts\DomainEvent\DomainEventInterface;
use PlayOrPay\Domain\Event\EventPickPlayedStatus;

class PickPlayedStatusChanged implements DomainEventInterface
{
    /** @var EventPickPlayedStatus */
    private $from;

    /** @var EventPickPlayedStatus */
    private $to;

    public function __construct(EventPickPlayedStatus $from, EventPickPlayedStatus $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function serialize()
    {
        return json_encode([
            'from' => (string) $this->from,
            'to'   => (string) $this->to,
        ]);
    }

    public function unserialize($serialized)
    {
        // TODO
    }
}

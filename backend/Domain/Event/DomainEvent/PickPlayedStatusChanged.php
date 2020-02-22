<?php

namespace PlayOrPay\Domain\Event\DomainEvent;

use PlayOrPay\Domain\Contracts\DomainEvent\DomainEventInterface;
use PlayOrPay\Domain\Event\EventPick;
use PlayOrPay\Domain\Event\EventPickPlayedStatus;

class PickPlayedStatusChanged implements DomainEventInterface
{
    /** @var EventPick */
    private $pick;

    /** @var EventPickPlayedStatus */
    private $from;

    /** @var EventPickPlayedStatus */
    private $to;

    public function __construct(EventPick $pick, EventPickPlayedStatus $from, EventPickPlayedStatus $to)
    {
        $this->pick = $pick;
        $this->from = $from;
        $this->to = $to;
    }

    public function jsonSerialize()
    {
        return [
            'event' => (string) $this->pick->getEvent()->getUuid(),
            'pick' => (string) $this->pick->getUuid(),
            'participant' => (string) $this->pick->getParticipant()->getUuid(),
            'participantUser' => (string) $this->pick->getParticipant()->getUser()->getSteamId(),
            'game' => (string) $this->pick->getGame()->getId(),
            'from' => (string) $this->from,
            'to'   => (string) $this->to,
        ];
    }
}

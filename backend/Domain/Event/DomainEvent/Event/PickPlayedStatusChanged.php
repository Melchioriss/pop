<?php

namespace PlayOrPay\Domain\Event\DomainEvent\Event;

use PlayOrPay\Domain\Contracts\DomainEvent\DomainEventInterface;
use PlayOrPay\Domain\Event\Event;
use PlayOrPay\Domain\Event\EventParticipant;
use PlayOrPay\Domain\Event\EventPick;
use PlayOrPay\Domain\Event\EventPickPlayedStatus;
use PlayOrPay\Domain\Game\Game;
use PlayOrPay\Domain\User\User;

class PickPlayedStatusChanged implements DomainEventInterface
{
    /** @var EventPick */
    public $pick;

    /** @var EventPickPlayedStatus */
    public $from;

    /** @var EventPickPlayedStatus */
    public $to;

    public function __construct(EventPick $pick, EventPickPlayedStatus $from, EventPickPlayedStatus $to)
    {
        $this->pick = $pick;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize()
    {
        return [
            'event'           => $this->pick->getEvent()->getUuid()->toString(),
            'pick'            => $this->pick->getUuid()->toString(),
            'participant'     => $this->pick->getParticipant()->getUuid()->toString(),
            'participantUser' => (string) $this->pick->getParticipant()->getUser()->getSteamId(),
            'game'            => (string) $this->pick->getGame()->getId(),
            'from'            => (string) $this->from,
            'to'              => (string) $this->to,
        ];
    }

    public static function refsMap(): array
    {
        return [
            'event'           => Event::class,
            'pick'            => EventPick::class,
            'participant'     => EventParticipant::class,
            'participantUser' => User::class,
            'game'            => Game::class,
        ];
    }
}

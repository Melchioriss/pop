<?php

namespace PlayOrPay\Application\Command\Event\Event\AddParticipant;

use PlayOrPay\Domain\Steam\SteamId;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AddEventParticipantCommand
{
    /** @var UuidInterface */
    public $eventUuid;

    /** @var UuidInterface */
    public $participantUuid;

    /** @var SteamId */
    public $steamId;

    public function __construct(string $eventUuid, string $participantUuid, int $steamId)
    {
        $this->eventUuid = Uuid::fromString($eventUuid);
        $this->participantUuid = Uuid::fromString($participantUuid);
        $this->steamId = new SteamId($steamId);
    }
}

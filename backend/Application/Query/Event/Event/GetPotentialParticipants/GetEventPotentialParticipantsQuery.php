<?php

namespace PlayOrPay\Application\Query\Event\Event\GetPotentialParticipants;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class GetEventPotentialParticipantsQuery
{
    /** @var UuidInterface */
    public $eventUuid;

    public function __construct(string $eventUuid)
    {
        $this->eventUuid = Uuid::fromString($eventUuid);
    }
}

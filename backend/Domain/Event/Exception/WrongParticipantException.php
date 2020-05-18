<?php

namespace PlayOrPay\Domain\Event\Exception;

use DomainException;
use Ramsey\Uuid\UuidInterface;

class WrongParticipantException extends DomainException
{
    public static function becauseTheyDontBelongToEvent(UuidInterface $participantUuid, UuidInterface $eventUuid): self
    {
        return new self(sprintf("Requested participant '%s' doesn't belong to event '%s'", $participantUuid->toString(), $eventUuid->toString()));
    }
}

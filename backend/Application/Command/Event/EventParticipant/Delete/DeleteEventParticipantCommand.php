<?php

namespace PlayOrPay\Application\Command\Event\EventParticipant\Delete;

use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DeleteEventParticipantCommand
{
    /** @var UuidInterface */
    public $participantUuid;

    public function __construct(string $participantUuid)
    {
        Assert::that($participantUuid)->uuid();

        $this->participantUuid = Uuid::fromString($participantUuid);
    }
}

<?php

namespace PlayOrPay\Application\Command\Event\EventParticipant\UpdateGroupWins;

use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UpdateEventParticipantGroupWinsCommand
{
    /** @var UuidInterface */
    private $participantUuid;

    /** @var string */
    private $groupWins;

    public function __construct(string $participantUuid, string $groupWins)
    {
        Assert::that($participantUuid)->uuid();

        $this->participantUuid = Uuid::fromString($participantUuid);
        $this->groupWins = $groupWins;
    }

    public function getParticipantUuid(): UuidInterface
    {
        return $this->participantUuid;
    }

    public function getGroupWins(): string
    {
        return $this->groupWins;
    }
}

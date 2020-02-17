<?php

namespace PlayOrPay\Application\Command\Event\EventParticipant\UpdateBlaeoGames;

use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UpdateEventParticipantBlaeoGamesCommand
{
    /** @var UuidInterface */
    private $participantUuid;

    /** @var string */
    private $blaeoGames;

    public function __construct(string $participantUuid, string $blaeoGames)
    {
        Assert::that($participantUuid)->uuid();
        $this->participantUuid = Uuid::fromString($participantUuid);
        $this->blaeoGames = $blaeoGames;
    }

    public function getParticipantUuid(): UuidInterface
    {
        return $this->participantUuid;
    }

    public function getBlaeoGames(): string
    {
        return $this->blaeoGames;
    }
}

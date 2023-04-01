<?php

namespace PlayOrPay\Application\Command\Event\EventParticipant\UpdateBlaeoPoints;

use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UpdateEventParticipantBlaeoPointsCommand
{
    /** @var UuidInterface */
    public $participantUuid;

    /** @var float */
    public $blaeoPoints;

    public function __construct(string $participantUuid, float $blaeoPoints)
    {
        Assert::that($blaeoPoints)->greaterOrEqualThan(0);
        $this->participantUuid = Uuid::fromString($participantUuid);
        $this->blaeoPoints = $blaeoPoints;
    }
}

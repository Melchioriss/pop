<?php

namespace PlayOrPay\Application\Command\Event\EventParticipant\UpdateExtraRules;

use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UpdateEventParticipantExtraRulesCommand
{
    /** @var UuidInterface */
    private $participantUuid;

    /** @var string */
    private $extraRules;

    public function __construct(string $participantUuid, string $extraRules)
    {
        Assert::that($participantUuid)->uuid();

        $this->participantUuid = Uuid::fromString($participantUuid);
        $this->extraRules = $extraRules;
    }

    public function getParticipantUuid(): UuidInterface
    {
        return $this->participantUuid;
    }

    public function getExtraRules(): string
    {
        return $this->extraRules;
    }
}

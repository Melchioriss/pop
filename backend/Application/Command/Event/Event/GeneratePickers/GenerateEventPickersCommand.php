<?php

namespace PlayOrPay\Application\Command\Event\Event\GeneratePickers;

use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class GenerateEventPickersCommand
{
    /** @var UuidInterface */
    private $eventUuid;

    public function __construct(string $eventUuid)
    {
        Assert::that($eventUuid)->uuid();

        $this->eventUuid = Uuid::fromString($eventUuid);
    }

    public function getEventUuid(): UuidInterface
    {
        return $this->eventUuid;
    }
}

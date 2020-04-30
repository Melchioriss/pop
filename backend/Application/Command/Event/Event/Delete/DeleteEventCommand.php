<?php

namespace PlayOrPay\Application\Command\Event\Event\Delete;

use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DeleteEventCommand
{
    /** @var UuidInterface */
    public $eventUuid;

    public function __construct(string $eventUuid)
    {
        Assert::that($eventUuid)->uuid();

        $this->eventUuid = Uuid::fromString($eventUuid);
    }
}

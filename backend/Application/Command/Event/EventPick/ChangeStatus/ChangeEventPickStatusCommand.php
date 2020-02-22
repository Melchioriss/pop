<?php

namespace PlayOrPay\Application\Command\Event\EventPick\ChangeStatus;

use Assert\Assert;
use PlayOrPay\Domain\Event\EventPickPlayedStatus;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ChangeEventPickStatusCommand
{
    /** @var UuidInterface */
    public $pickUuid;

    /** @var EventPickPlayedStatus */
    public $status;

    public function __construct(string $pickUuid, int $status)
    {
        Assert::that($pickUuid)->uuid();

        $this->pickUuid = Uuid::fromString($pickUuid);
        $this->status = new EventPickPlayedStatus($status);
    }

    public function getPickUuid(): UuidInterface
    {
        return $this->pickUuid;
    }

    public function getStatus(): EventPickPlayedStatus
    {
        return $this->status;
    }
}

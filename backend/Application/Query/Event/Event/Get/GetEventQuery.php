<?php

namespace PlayOrPay\Application\Query\Event\Event\Get;

use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class GetEventQuery
{
    /** @var UuidInterface */
    private $uuid;

    public function __construct(string $uuid)
    {
        Assert::that($uuid)->uuid();
        $this->uuid = Uuid::fromString($uuid);
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }
}

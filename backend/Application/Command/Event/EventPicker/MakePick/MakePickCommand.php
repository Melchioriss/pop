<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\MakePick;

use Assert\Assert;
use PlayOrPay\Domain\Event\EventPickType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MakePickCommand
{
    /** @var UuidInterface */
    public $pickUuid;

    /** @var UuidInterface */
    public $pickerUuid;

    /** @var EventPickType */
    public $type;

    /** @var int */
    public $gameId;

    public function __construct(string $pickUuid, string $pickerUuid, int $type, int $gameId)
    {
        Assert::that($gameId)->greaterThan(0);
        Assert::that($pickUuid)->uuid();
        Assert::that($pickerUuid)->uuid();

        $this->pickUuid = Uuid::fromString($pickUuid);
        $this->pickerUuid = Uuid::fromString($pickerUuid);
        $this->type = new EventPickType($type);
        $this->gameId = $gameId;
    }
}

<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\ChangePick;

use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ChangePickCommand
{
    /** @var UuidInterface */
    public $pickUuid;

    /** @var UuidInterface */
    public $pickerUuid;

    /** @var int */
    public $gameId;

    public function __construct(string $pickUuid, string $pickerUuid, int $gameId)
    {
        Assert::that($gameId)->greaterThan(0);
        Assert::that($pickUuid)->uuid();
        Assert::that($pickerUuid)->uuid();

        $this->pickUuid = Uuid::fromString($pickUuid);
        $this->pickerUuid = Uuid::fromString($pickerUuid);
        $this->gameId = $gameId;
    }
}

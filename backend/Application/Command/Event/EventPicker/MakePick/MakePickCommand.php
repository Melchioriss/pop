<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\MakePick;

use Assert\Assert;
use PlayOrPay\Domain\Event\EventPickType;
use PlayOrPay\Domain\Game\GameId;
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

    /** @var GameId */
    public $gameId;

    public function __construct(string $pickUuid, string $pickerUuid, int $type, string $gameId)
    {
        Assert::lazy()
            ->that($gameId)->greaterThan(0)
            ->that($pickUuid)->uuid()
            ->that($pickerUuid)->uuid()
            ->verifyNow();

        $this->pickUuid = Uuid::fromString($pickUuid);
        $this->pickerUuid = Uuid::fromString($pickerUuid);
        $this->type = new EventPickType($type);
        $this->gameId = GameId::fromComplexId($gameId);
    }
}

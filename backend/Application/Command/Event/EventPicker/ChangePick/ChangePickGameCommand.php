<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\ChangePick;

use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ChangePickGameCommand
{
    /** @var UuidInterface */
    public $pickUuid;

    /** @var int */
    public $gameId;

    public function __construct(string $pickUuid, int $gameId)
    {
        Assert::that($gameId)->greaterThan(0);

        $this->pickUuid = Uuid::fromString($pickUuid);
        $this->gameId = $gameId;
    }
}

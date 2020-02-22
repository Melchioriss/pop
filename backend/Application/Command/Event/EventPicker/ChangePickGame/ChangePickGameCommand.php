<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\ChangePickGame;

use PlayOrPay\Domain\Game\GameId;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ChangePickGameCommand
{
    /** @var UuidInterface */
    public $pickUuid;

    /** @var GameId */
    public $gameId;

    public function __construct(string $pickUuid, string $gameId)
    {
        $this->pickUuid = Uuid::fromString($pickUuid);
        $this->gameId = GameId::fromComplexId($gameId);
    }
}

<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\UpdateUser;

use Assert\Assert;
use PlayOrPay\Domain\Steam\SteamId;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UpdateEventPickerUserCommand
{
    /** @var UuidInterface */
    public $pickerUuid;

    /** @var SteamId */
    public $userId;

    public function __construct(string $pickerUuid, string $userId)
    {
        Assert::that($pickerUuid)->uuid();

        $this->pickerUuid = Uuid::fromString($pickerUuid);
        $this->userId = new SteamId((int) $userId);
    }
}

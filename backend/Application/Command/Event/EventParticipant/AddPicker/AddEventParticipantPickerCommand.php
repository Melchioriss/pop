<?php

namespace PlayOrPay\Application\Command\Event\EventParticipant\AddPicker;

use Assert\Assert;
use PlayOrPay\Domain\Event\EventPickerType;
use PlayOrPay\Domain\Steam\SteamId;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AddEventParticipantPickerCommand
{
    /** @var UuidInterface */
    private $participantUuid;

    /** @var UuidInterface */
    private $pickerUuid;

    /** @var SteamId */
    private $userId;

    /** @var EventPickerType */
    private $pickerType;

    public function __construct(string $participantUuid, string $pickerUuid, string $userId, int $type)
    {
        Assert::that($participantUuid)->uuid();
        Assert::that($pickerUuid)->uuid();
        $this->participantUuid = Uuid::fromString($participantUuid);
        $this->pickerUuid = Uuid::fromString($pickerUuid);
        $this->userId = new SteamId((int) $userId);
        $this->pickerType = new EventPickerType($type);
    }

    public function getParticipantUuid(): UuidInterface
    {
        return $this->participantUuid;
    }

    public function getPickerUuid(): UuidInterface
    {
        return $this->pickerUuid;
    }

    public function getUserId(): SteamId
    {
        return $this->userId;
    }

    public function getPickerType(): EventPickerType
    {
        return $this->pickerType;
    }
}

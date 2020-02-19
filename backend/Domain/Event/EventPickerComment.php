<?php

namespace PlayOrPay\Domain\Event;

use DateTimeImmutable;
use PlayOrPay\Domain\User\User;
use Ramsey\Uuid\UuidInterface;

class EventPickerComment
{
    /** @var UuidInterface */
    private $uuid;

    /** @var EventPicker */
    private $picker;

    /** @var User */
    private $user;

    /** @var string */
    private $text;

    /** @var DateTimeImmutable */
    private $createdAt;

    public function __construct(UuidInterface $uuid, EventPicker $picker, User $user, string $text)
    {
        $this->uuid = $uuid;
        $this->picker = $picker;
        $this->user = $user;
        $this->text = $text;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getUser(): User
    {
        return $this->user;
    }
}

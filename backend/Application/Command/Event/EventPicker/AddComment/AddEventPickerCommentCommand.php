<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\AddComment;

use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AddEventPickerCommentCommand
{
    /** @var UuidInterface */
    public $commentUuid;

    /** @var UuidInterface */
    public $pickerUuid;

    /** @var string */
    public $text;

    /** @var UuidInterface|null */
    public $reviewedPickUuid;

    public function __construct(
        string $commentUuid,
        string $pickerUuid,
        string $text,
        string $reviewedPickUuid = null
    ) {
        Assert::that($text)->minLength(1);

        $this->commentUuid = Uuid::fromString($commentUuid);
        $this->pickerUuid = Uuid::fromString($pickerUuid);
        $this->reviewedPickUuid = $reviewedPickUuid ? Uuid::fromString($reviewedPickUuid) : null;
        $this->text = $text;
    }
}

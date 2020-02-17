<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\AddComment;

use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AddEventPickerCommentCommand
{
    /** @var UuidInterface */
    //public $commentUuid;

    /** @var UuidInterface */
    public $pickerUuid;

    /** @var string */
    public $text;

    public function __construct(/*string $commentUuid, */string $pickerUuid, string $text)
    {
        Assert::that($text)->minLength(1);

        //$this->commentUuid = Uuid::fromString($commentUuid);
        $this->pickerUuid = Uuid::fromString($pickerUuid);
        $this->text = $text;
    }
}

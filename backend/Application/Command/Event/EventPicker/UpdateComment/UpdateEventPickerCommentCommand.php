<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\UpdateComment;

use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UpdateEventPickerCommentCommand
{
    /** @var UuidInterface */
    public $commentUuid;

    /** @var string */
    public $text;

    public function __construct(string $commentUuid, string $text)
    {
        Assert::that($text)->minLength(1);

        $this->commentUuid = Uuid::fromString($commentUuid);
        $this->text = $text;
    }
}

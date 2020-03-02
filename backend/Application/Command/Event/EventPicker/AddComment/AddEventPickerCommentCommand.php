<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\AddComment;

use Assert\Assert;
use Exception;
use PlayOrPay\Domain\Event\EventCommentGameReferenceType;
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
    public $referencedPickUuid;

    /** @var EventCommentGameReferenceType|null */
    public $gameReferenceType;

    /**
     * @param string $commentUuid
     * @param string $pickerUuid
     * @param string $text
     * @param string|null $referencedPickUuid
     * @param int|null $gameReferenceType
     *
     * @throws Exception
     */
    public function __construct(
        string $commentUuid,
        string $pickerUuid,
        string $text,
        ?string $referencedPickUuid = null,
        ?int $gameReferenceType = null
    ) {
        Assert::that($text)->minLength(1);

        if ($referencedPickUuid && $gameReferenceType === null) {
            throw new Exception("You can't reference to a game without defining reference type");
        }

        $this->commentUuid = Uuid::fromString($commentUuid);
        $this->pickerUuid = Uuid::fromString($pickerUuid);
        $this->referencedPickUuid = $referencedPickUuid ? Uuid::fromString($referencedPickUuid) : null;
        $this->gameReferenceType = $gameReferenceType === null
            ? null
            : new EventCommentGameReferenceType($gameReferenceType);
        $this->text = $text;
    }
}

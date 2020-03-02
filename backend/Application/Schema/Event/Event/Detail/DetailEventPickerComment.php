<?php

namespace PlayOrPay\Application\Schema\Event\Event\Detail;

class DetailEventPickerComment
{
    /** @var string */
    public $uuid;

    /** @var string */
    public $user;

    /** @var string */
    public $text;

    /** @var string */
    public $createdAt;

    /** @var int|null */
    public $gameReferenceType;

    /** @var string|null */
    public $referencedGame;

    /** @var string|null */
    public $referencedPick;

    /** @var string|null */
    public $updatedAt;
}

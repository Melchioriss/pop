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

    /** @var string */
    public $reviewedGame;

    /** @var string */
    public $reviewedPickUuid;

    /** @var string|null */
    public $updatedAt;
}

<?php

namespace PlayOrPay\Application\Schema\Event\Event\Detail;

use DateTimeImmutable;

class DetailEventPickerComment
{
    /** @var string */
    public $uuid;

    /** @var string */
    public $user;

    /** @var string */
    public $text;

    /** @var DateTimeImmutable */
    public $createdAt;

    /** @var string */
    public $reviewedGame;

    /** @var string */
    public $reviewedPickUuid;
}

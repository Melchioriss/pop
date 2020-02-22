<?php

namespace PlayOrPay\Application\Schema\Event\Event\Detail;

use DateTimeImmutable;

class DetailEventPickerComment
{
    /** @var string */
    public $user;

    /** @var string */
    public $text;

    /** @var DateTimeImmutable */
    public $createdAt;

    /** @var int */
    public $reviewedGame;
}

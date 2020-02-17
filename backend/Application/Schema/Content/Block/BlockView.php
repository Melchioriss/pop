<?php

namespace PlayOrPay\Application\Schema\Content\Block;

use DateTimeImmutable;

class BlockView
{
    /** @var DateTimeImmutable */
    public $createdAt;

    /** @var DateTimeImmutable */
    public $updatedAt;

    /** @var string */
    public $code;

    /** @var string */
    public $content;
}

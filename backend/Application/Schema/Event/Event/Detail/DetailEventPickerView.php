<?php

namespace PlayOrPay\Application\Schema\Event\Event\Detail;

class DetailEventPickerView
{
    /** @var string */
    public $uuid;

    /** @var string */
    public $user;

    /** @var string */
    public $type;

    /** @var DetailEventPickView[] */
    public $picks;

    /** @var DetailEventPickerComment[] */
    public $comments;
}

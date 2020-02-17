<?php

namespace PlayOrPay\Application\Schema\Event\Event\Detail;

class DetailEventUserView
{
    /** @var string */
    public $steamId;

    /** @var string */
    public $profileName;

    /** @var string */
    public $profileUrl;

    /** @var string */
    public $avatar;

    /** @var string|null */
    public $blaeoName;
}

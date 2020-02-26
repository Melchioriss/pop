<?php

namespace PlayOrPay\Application\Schema\DomainEvent\Activity;

class ActivityUser
{
    /** @var string */
    public $steamId;

    /** @var string */
    public $profileName;

    /** @var string */
    public $avatar;

    /** @var string */
    public $profileUrl;
}

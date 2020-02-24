<?php

namespace PlayOrPay\Application\Schema\DomainEvent\Log;

class LogUser
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

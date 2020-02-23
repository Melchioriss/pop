<?php

namespace PlayOrPay\Application\Schema\Event\Event\Detail;

class DetailEventEarnedReward
{
    /** @var string|null */
    public $pick;

    /** @var int */
    public $reason;

    /** @var int */
    public $value;
}

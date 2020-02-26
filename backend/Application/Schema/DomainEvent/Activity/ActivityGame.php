<?php

namespace PlayOrPay\Application\Schema\DomainEvent\Activity;

class ActivityGame
{
    /** @var string */
    public $id;

    /** @var int */
    public $localId;

    /** @var string */
    public $name;

    /** @var int */
    public $achievements;
}

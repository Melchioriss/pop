<?php

namespace PlayOrPay\Application\Schema\DomainEvent\Log;

class LogGame
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

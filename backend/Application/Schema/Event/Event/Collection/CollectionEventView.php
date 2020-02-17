<?php

namespace PlayOrPay\Application\Schema\Event\Event\Collection;

use League\Period\Period;

class CollectionEventView
{
    /** @var string */
    public $uuid;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var Period */
    public $activePeriod;
}

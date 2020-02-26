<?php

namespace PlayOrPay\Application\Schema\DomainEvent\Activity;

class CollectionDomainEventRecord
{
    /** @var string */
    public $uuid;

    /** @var string */
    public $actor;

    /** @var string */
    public $name;

    /** @var array<string, bool|int|string|null> */
    public $payload;

    /** @var string */
    public $createdAt;
}

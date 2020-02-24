<?php

namespace PlayOrPay\Domain\Contracts\DomainEvent;

use JsonSerializable;

interface DomainEventInterface extends JsonSerializable
{
    /**
     * @return array<string, string>
     */
    public static function refsMap(): array;
}

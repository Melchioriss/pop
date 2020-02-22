<?php

namespace PlayOrPay\Domain\DomainEvent;

use Exception;
use PlayOrPay\Domain\Contracts\DomainEvent\DomainEventInterface;
use PlayOrPay\Domain\Contracts\Entity\AggregateInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DomainEventRecord implements AggregateInterface
{
    /** @var UuidInterface */
    private $uuid;

    /** @var string */
    private $name;

    /** @var string */
    private $payload;

    /**
     * @param string $name
     * @param string $payload
     *
     * @throws Exception
     */
    public function __construct(string $name, string $payload)
    {
        $this->uuid = Uuid::uuid4();
        $this->name = $name;
        $this->payload = $payload;
    }

    /**
     * @param DomainEventInterface $event
     *
     * @throws Exception
     *
     * @return DomainEventRecord
     */
    public static function fromEvent(DomainEventInterface $event)
    {
        return new self(get_class($event), $event->serialize());
    }

    public function popDomainEvents(): array
    {
        // should not fire any events
        return [];
    }
}

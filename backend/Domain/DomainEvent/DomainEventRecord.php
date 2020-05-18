<?php

namespace PlayOrPay\Domain\DomainEvent;

use DateTimeImmutable;
use Exception;
use PlayOrPay\Domain\Contracts\DomainEvent\DomainEventInterface;
use PlayOrPay\Domain\Contracts\Entity\AggregateInterface;
use PlayOrPay\Domain\User\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DomainEventRecord implements AggregateInterface
{
    /** @var UuidInterface */
    private $uuid;

    /** @var User */
    private $actor;

    /** @var string */
    private $name;

    /** @var array<string, bool|int|string|null> */
    private $payload;

    /** @var DateTimeImmutable */
    private $createdAt;

    /**
     * @param string $name
     * @param User|null $actor
     * @param array<string, bool|int|string|null> $payload
     *
     * @throws Exception
     */
    public function __construct(string $name, ?User $actor, array $payload)
    {
        $this->uuid = Uuid::uuid4();
        $this->actor = $actor;
        $this->name = $name;
        $this->payload = $payload;
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @param DomainEventInterface $event
     * @param User|null $actor
     *
     * @throws Exception
     *
     * @return DomainEventRecord
     */
    public static function fromEvent(DomainEventInterface $event, ?User $actor): self
    {
        return new self(get_class($event), $actor, $event->jsonSerialize());
    }

    public function popDomainEvents(): array
    {
        // should not fire any events
        return [];
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getActor(): User
    {
        return $this->actor;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, bool|int|string|null>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}

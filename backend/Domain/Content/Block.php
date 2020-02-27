<?php

namespace PlayOrPay\Domain\Content;

use Assert\Assert;
use DateTimeImmutable;
use PlayOrPay\Domain\Contracts\Entity\AggregateInterface;
use PlayOrPay\Domain\Contracts\Entity\AggregateTrait;
use PlayOrPay\Domain\Contracts\Entity\OnUpdateEventListenerInterface;

class Block implements OnUpdateEventListenerInterface, AggregateInterface
{
    use AggregateTrait;

    /** @var string */
    private $code;

    /** @var string */
    private $content;

    /** @var DateTimeImmutable */
    private $createdAt;

    /** @var DateTimeImmutable|null */
    private $updatedAt;

    public function __construct(string $code, string $content)
    {
        Assert::thatAll([$code, $content])->minLength(1);
        $this->code = $code;
        $this->content = $content;
        $this->createdAt = new DateTimeImmutable();
    }

    public function updateContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function onUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}

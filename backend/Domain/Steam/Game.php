<?php

namespace PlayOrPay\Domain\Steam;

use Assert\Assert;
use PlayOrPay\Domain\Contracts\Entity\AggregateInterface;
use PlayOrPay\Domain\Contracts\Entity\AggregateTrait;

class Game implements AggregateInterface
{
    use AggregateTrait;

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var int|null */
    private $achievements;

    public function __construct(int $id, string $name)
    {
        Assert::that($id)->greaterThan(0);

        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function updateName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAchievements(): ?int
    {
        return $this->achievements;
    }

    public function updateAchievements(?int $achievements): self
    {
        $this->achievements = $achievements;

        return $this;
    }
}

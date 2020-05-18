<?php

namespace PlayOrPay\Domain\Game;

use PlayOrPay\Domain\Contracts\Entity\AggregateInterface;
use PlayOrPay\Domain\Contracts\Entity\AggregateTrait;

class Game implements AggregateInterface
{
    use AggregateTrait;

    /** @var string */
    private $complexId;

    /** @var GameId */
    private $id;

    /** @var string */
    private $name;

    /** @var int|null */
    private $achievements;

    public function __construct(GameId $id, string $name)
    {
        $this->id = $id;
        $this->complexId = (string) $id;
        $this->name = $name;
    }

    public static function fromExpandedGameId(StoreId $storeId, string $localId, string $name): self
    {
        return new self(new GameId($storeId, $localId), $name);
    }

    public function getId(): GameId
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

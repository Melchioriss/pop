<?php

namespace PlayOrPay\Domain\Game;

use Assert\Assert;

class GameId
{
    /** @var StoreId */
    private $storeId;

    /** @var string */
    private $localId;

    public function __construct(StoreId $storeId, string $localId)
    {
        Assert::that($localId)->minLength(1);

        $this->storeId = $storeId;
        $this->localId = $localId;
    }

    public static function fromComplexId(string $complexId): self
    {
        Assert::that($complexId)->minLength(3)->contains(':');
        [$storeId, $localId] = explode(':', $complexId);

        return new self(new StoreId((int) $storeId), $localId);
    }

    public function __toString()
    {
        return "{$this->storeId}:{$this->localId}";
    }

    public function getStoreId(): StoreId
    {
        return $this->storeId;
    }

    public function getLocalId(): string
    {
        return $this->localId;
    }

    public function equalTo(self $otherGameId): bool
    {
        return $this->localId === $otherGameId->localId
            && $this->storeId->equalTo($otherGameId->storeId);
    }
}

<?php

namespace PlayOrPay\Domain\Event;

use Assert\Assert;
use Doctrine\Common\Collections\ArrayCollection;
use DomainException;
use Exception;
use Insideone\Package\EnumFramework\AmbiguousValueException;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Domain\Game\Game;
use PlayOrPay\Domain\Game\GameId;
use PlayOrPay\Domain\Game\StoreId;
use PlayOrPay\Domain\Steam\SteamId;
use PlayOrPay\Domain\User\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

class EventParticipant
{
    /** @var UuidInterface */
    private $uuid;

    /** @var Event */
    private $event;

    /** @var User */
    private $user;

    /** @var bool */
    private $active;

    /** @var string */
    private $groupWins;

    /** @var string */
    private $extraRules;

    /** @var string */
    private $blaeoGames;

    /** @var EventPicker[]|ArrayCollection<int, EventPicker> */
    private $pickers;

    /** @var EventEarnedReward[]|ArrayCollection<int, EventEarnedReward> */
    private $rewards;

    public function __construct(
        UuidInterface $uuid,
        Event $event,
        User $user,
        string $groupWins,
        string $blaeoGames,
        string $extraRules,
        bool $active = true
    ) {
        $this->uuid = $uuid;
        $this->event = $event;
        $this->user = $user;
        $this->groupWins = $groupWins;
        $this->blaeoGames = $blaeoGames;
        $this->extraRules = $extraRules;
        $this->active = $active;
        $this->pickers = new ArrayCollection();
        $this->rewards = new ArrayCollection();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserSteamId(): SteamId
    {
        return $this->user->getId();
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function updateGroupWins(string $groupWins): self
    {
        $this->groupWins = $groupWins;

        return $this;
    }

    public function updateBlaeoGames(string $blaeoGames): self
    {
        $this->blaeoGames = $blaeoGames;

        return $this;
    }

    public function updateExtraRules(string $extraRules): self
    {
        $this->extraRules = $extraRules;

        return $this;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @param EventPicker $picker
     *
     * @throws AmbiguousValueException
     * @throws ReflectionException
     *
     * @return EventParticipant
     */
    public function addPicker(EventPicker $picker): self
    {
        $existsPicker = $this->findPickerOfType($picker->getType());
        if ($existsPicker) {
            throw new DomainException(sprintf("Participant '%s' already has picker '%s' of type '%s'", $this->getUser()->getUsername(), $picker->getUser()->getUsername(), $picker->getType()->getCodename()));
        }
        if ($this->pickers->contains($picker)) {
            throw new DomainException(sprintf("Participant '%s' already has picker '%s'", $this->getUser()->getUsername(), $picker->getUser()->getUsername()));
        }

        $this->pickers->add($picker);

        return $this;
    }

    public function findPickerOfType(EventPickerType $pickerType): ?EventPicker
    {
        foreach ($this->pickers as $picker) {
            if ($picker->getType()->equalTo($pickerType)) {
                return $picker;
            }
        }

        return null;
    }

    /**
     * @param EventPicker[] $pickers
     *
     * @throws AmbiguousValueException
     * @throws ReflectionException
     *
     * @return self
     */
    public function addPickers(array $pickers): self
    {
        foreach ($pickers as $picker) {
            $this->addPicker($picker);
        }

        return $this;
    }

    /**
     * @return EventPicker[]
     */
    public function getPickers(): array
    {
        return $this->pickers->toArray();
    }

    /**
     * @param StoreId|null $ofStore
     *
     * @return Game[]
     */
    public function getGames(?StoreId $ofStore = null): array
    {
        $games = [];
        foreach ($this->pickers as $picker) {
            array_push($games, ...$picker->getGames($ofStore));
        }

        return $games;
    }

    /**
     * @return GameId[]
     */
    public function getGameIds(): array
    {
        return array_map(function (Game $game) {
            return $game->getId();
        }, $this->getGames());
    }

    /**
     * @param StoreId $storeId
     *
     * @return string[]
     */
    public function getLocalGameIds(StoreId $storeId): array
    {
        $ids = [];
        foreach ($this->getGames($storeId) as $game) {
            $ids[] = $game->getId()->getLocalId();
        }

        return $ids;
    }

    public function hasPicks(): bool
    {
        return count($this->getGames()) > 0;
    }

    /**
     * @param UuidInterface $uuid
     *
     * @throws NotFoundException
     *
     * @return EventPick
     */
    public function getPick(UuidInterface $uuid): EventPick
    {
        $pick = $this->findPick($uuid);
        if (!$pick) {
            throw NotFoundException::forObject(EventPick::class, $uuid->toString());
        }

        return $pick;
    }

    public function findPick(UuidInterface $uuid): ?EventPick
    {
        foreach ($this->pickers as $picker) {
            if ($pick = $picker->findPick($uuid)) {
                return $pick;
            }
        }

        return null;
    }

    public function getPickForGame(GameId $gameId): EventPick
    {
        $pick = $this->findPickOfGame($gameId);

        if ($pick) {
            return $pick;
        }

        throw new DomainException(sprintf("Participant '%s' doesn't have pick for game '%s'", $this->getUser()->getProfileName(), $gameId));
    }

    public function findPickOfGame(GameId $gameId): ?EventPick
    {
        foreach ($this->pickers as $picker) {
            if ($pick = $picker->findPickOfGame($gameId)) {
                return $pick;
            }
        }

        return null;
    }

    public function updatePlaytimeForGame(GameId $gameId, int $playtime): self
    {
        $this->getPickForGame($gameId)->updatePlaytime($playtime);

        return $this;
    }

    public function updateAchievementsForGame(GameId $gameId, int $achievements): self
    {
        $this->getPickForGame($gameId)->updateAchievements($achievements);

        return $this;
    }

    /**
     * @param EventReward $blaeoGamesReward
     * @param int $value
     *
     * @throws Exception
     *
     * @return EventParticipant
     */
    public function updateBlaeoPoints(EventReward $blaeoGamesReward, int $value): self
    {
        Assert::lazy()
            ->that($value)->greaterOrEqualThan(0)
            ->that($blaeoGamesReward->getReason()->__default)->eq(RewardReason::BLAEO_GAMES)
            ->verifyNow();

        if ($value > 0) {
            $this->setupReward($blaeoGamesReward, null, $value);
        } else {
            $this->removeReward($blaeoGamesReward->getReason(), null);
        }

        return $this;
    }

    public function findReward(RewardReason $desiredReason, ?UuidInterface $pickUuid): ?EventEarnedReward
    {
        foreach ($this->rewards as $earnedReward) {
            if ($earnedReward->isFor($desiredReason, $pickUuid)) {
                return $earnedReward;
            }
        }

        return null;
    }

    /**
     * @param RewardReason $desiredReason
     * @param UuidInterface|null $pickUuid
     *
     * @throws AmbiguousValueException
     * @throws NotFoundException
     * @throws ReflectionException
     *
     * @return EventEarnedReward
     */
    public function getReward(RewardReason $desiredReason, ?UuidInterface $pickUuid): EventEarnedReward
    {
        $reward = $this->findReward($desiredReason, $pickUuid);
        if (!$reward) {
            throw NotFoundException::forQuery(EventEarnedReward::class, ['reason' => $desiredReason->getCodename(), 'pick' => $pickUuid->toString()]);
        }

        return $reward;
    }

    /**
     * @param RewardReason $reason
     * @param UuidInterface|null $pickUuid
     *
     * @throws AmbiguousValueException
     * @throws ReflectionException
     *
     * @return EventParticipant
     */
    public function removeReward(RewardReason $reason, ?UuidInterface $pickUuid): self
    {
        $earnedReward = $this->findReward($reason, $pickUuid);
        if (!$earnedReward) {
            throw new DomainException(sprintf("Such a reward for pick '%s' and reason '%s' doesn't exist", $pickUuid->toString(), $reason->getCodename()));
        }

        $this->rewards->removeElement($earnedReward);

        return $this;
    }

    /**
     * @param EventReward $reward
     * @param UuidInterface $pickUuid
     * @param int|null $value
     *
     * @throws Exception
     *
     * @return EventParticipant
     */
    public function setupReward(EventReward $reward, ?UuidInterface $pickUuid, ?int $value = null): self
    {
        $earnedReward = $this->findReward($reward->getReason(), $pickUuid);
        if ($earnedReward) {
            if ($value !== null && $earnedReward->getValue() !== $value) {
                $earnedReward->updateValue($value);
            }

            return $this;
        }

        $this->addReward($reward, $pickUuid, $value);

        return $this;
    }

    /**
     * @param EventReward $reward
     * @param UuidInterface|null $pickUuid
     * @param int|null $value
     *
     * @throws Exception
     *
     * @return EventParticipant
     */
    public function addReward(EventReward $reward, ?UuidInterface $pickUuid, ?int $value = null): self
    {
        $reward = $this->makeReward($reward, $pickUuid, $value);
        $this->rewards->add($reward);

        return $this;
    }

    /**
     * @param EventReward $reward
     * @param UuidInterface $pickUuid
     * @param int|null $value
     *
     * @throws Exception
     *
     * @return EventEarnedReward
     */
    private function makeReward(EventReward $reward, ?UuidInterface $pickUuid, ?int $value = null): EventEarnedReward
    {
        $pick = $pickUuid ? $this->getPick($pickUuid) : null;

        return new EventEarnedReward(Uuid::uuid4(), $this, $pick, $reward, $value);
    }

    /**
     * @return EventEarnedReward[]
     */
    public function getRewards(): array
    {
        return $this->rewards->toArray();
    }

    /**
     * @param UuidInterface|null $pickUuid
     *
     * @return EventEarnedReward[]
     */
    public function findRewardsOfPick(?UuidInterface $pickUuid): array
    {
        $suitableRewards = [];
        foreach ($this->rewards as $reward) {
            if ($reward->isForPick($pickUuid)) {
                $suitableRewards[] = $reward;
            }
        }

        return $suitableRewards;
    }

    /**
     * @param EventEarnedReward[] $rewards
     */
    public function removeRewards(array $rewards): void
    {
        Assert::thatAll($rewards)->isInstanceOf(EventEarnedReward::class);

        foreach ($rewards as $reward) {
            if (!$this->rewards->contains($reward)) {
                throw new DomainException("You're trying to remove reward that doesn't exist");
            }

            $this->rewards->removeElement($reward);
        }
    }

    /**
     * @return EventPick[]
     */
    public function getPicks(): array
    {
        $picks = [];
        foreach ($this->pickers as $picker) {
            array_push($picks, ...$picker->getPicks());
        }

        return $picks;
    }

    public function hasAllPicksMade(): bool
    {
        foreach ($this->pickers as $picker) {
            if (!$picker->hasDoneAllPicks()) {
                return false;
            }
        }

        return true;
    }

    public function hasBeatenAllPicks(): bool
    {
        if (!$this->hasAllPicksMade()) {
            return false;
        }

        foreach ($this->getPicks() as $pick) {
            if (!$pick->isBeaten(false)) {
                return false;
            }
        }

        return true;
    }

    public function hasReward(EventReward $reward, ?UuidInterface $pickUuid): bool
    {
        return (bool) $this->findReward($reward->getReason(), $pickUuid);
    }

    public function hasPickOfGame(GameId $gameId): bool
    {
        return (bool) $this->findPickOfGame($gameId);
    }

    private function assertNotHavingPickedGame(Game $game): void
    {
        if ($this->hasPickOfGame($game->getId())) {
            throw new DomainException(sprintf("Participant already has a pick for game '%s'", $game->getName()));
        }
    }

    /**
     * @param UuidInterface $pickUuid
     * @param Game $game
     *
     * @throws NotFoundException
     *
     * @return EventParticipant
     */
    public function changePickGame(UuidInterface $pickUuid, Game $game): self
    {
        $this->assertNotHavingPickedGame($game);

        $pick = $this->getPick($pickUuid);
        $pick->changeGame($game);

        return $this;
    }

    /**
     * @param UuidInterface $pickerUuid
     * @param UuidInterface $pickUuid
     * @param EventPickType $type
     * @param Game $game
     *
     * @throws NotFoundException
     *
     * @return EventPick
     */
    public function makePick(
        UuidInterface $pickerUuid,
        UuidInterface $pickUuid,
        EventPickType $type,
        Game $game
    ): EventPick {
        $this->assertNotHavingPickedGame($game);

        $picker = $this->getPicker($pickerUuid);

        return $picker->makePick($pickUuid, $type, $game);
    }

    /**
     * @param UuidInterface $pickerUuid
     *
     * @throws NotFoundException
     *
     * @return EventPicker
     */
    private function getPicker(UuidInterface $pickerUuid): EventPicker
    {
        foreach ($this->pickers as $picker) {
            if ($picker->getUuid()->equals($pickerUuid)) {
                return $picker;
            }
        }

        throw NotFoundException::forObject(EventPicker::class, $pickerUuid->toString());
    }
}

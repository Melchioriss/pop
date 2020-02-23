<?php

namespace PlayOrPay\Domain\Event;

use Assert\Assert;
use Doctrine\Common\Collections\ArrayCollection;
use DomainException;
use Exception;
use PlayOrPay\Domain\Game\Game;
use PlayOrPay\Domain\Game\GameId;
use PlayOrPay\Domain\Game\StoreId;
use PlayOrPay\Domain\Steam\SteamId;
use PlayOrPay\Domain\User\User;
use PlayOrPay\Package\EnumFramework\AmbiguousValueException;
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

    /** @var EventPicker[] */
    private $pickers;

    /** @var EventEarnedReward[] */
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

    public function getGameIds(): array
    {
        return array_map(function (Game $game) {
            return $game->getId();
        }, $this->getGames());
    }

    /**
     * @param StoreId $storeId
     * @return int[]
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
        foreach ($this->pickers as $picker) {
            if ($pick = $picker->findPickOfGame($gameId)) {
                return $pick;
            }
        }

        throw new DomainException(sprintf("Participant '%s' doesn't have pick for game '%s'", $this->getUser()->getProfileName(), $gameId));
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
            ->that((string) $blaeoGamesReward->getReason())->same(RewardReason::BLAEO_GAMES)
            ->verifyNow();

        if ($value > 0) {
            $this->setupReward($blaeoGamesReward, $value);
        } else {
            $this->removeReward($blaeoGamesReward->getReason());
        }

        return $this;
    }

    private function findReward(RewardReason $targetReason): ?EventEarnedReward
    {
        $targetReasonValue = (string) $targetReason;

        foreach ($this->rewards as $earnedReward) {
            if ((string) $earnedReward->getReason() === $targetReasonValue) {
                return $earnedReward;
            }
        }

        return null;
    }

    private function removeReward(RewardReason $reason): self
    {
        $earnedReward = $this->findReward($reason);
        if ($earnedReward) {
            $this->rewards->removeElement($earnedReward);
        }

        return $this;
    }

    /**
     * @param EventReward $reward
     * @param int|null $value
     *
     * @throws Exception
     *
     * @return EventParticipant
     */
    private function setupReward(EventReward $reward, ?int $value = null): self
    {
        $earnedReward = $this->findReward($reward->getReason());
        if ($earnedReward) {
            $earnedReward->updateValue($value);

            return $this;
        }

        $earnedReward = $this->makeReward($reward, $value);
        $this->insertReward($earnedReward);

        return $this;
    }

    private function insertReward(EventEarnedReward $achievement): self
    {
        $this->rewards->add($achievement);

        return $this;
    }

    /**
     * @param EventReward $reward
     * @param int|null $value
     *
     * @throws Exception
     *
     * @return EventEarnedReward
     */
    private function makeReward(EventReward $reward, ?int $value = null)
    {
        return new EventEarnedReward(Uuid::uuid4(), $this, $reward, $value);
    }
}

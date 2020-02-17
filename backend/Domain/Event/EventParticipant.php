<?php

namespace PlayOrPay\Domain\Event;

use DomainException;
use PlayOrPay\Domain\Steam\Game;
use PlayOrPay\Domain\Steam\SteamId;
use PlayOrPay\Domain\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

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

    public function __construct(UuidInterface $uuid, Event $event, User $user, string $groupWins, string $blaeoGames, string $extraRules, bool $active = true)
    {
        $this->uuid = $uuid;
        $this->event = $event;
        $this->user = $user;
        $this->groupWins = $groupWins;
        $this->blaeoGames = $blaeoGames;
        $this->extraRules = $extraRules;
        $this->active = $active;
        $this->pickers = new ArrayCollection;
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
     * @param EventPicker[] $pickers
     * @return EventParticipant
     */
    public function addPickers(array $pickers): EventParticipant
    {
        foreach ($pickers as $picker) {
            $this->pickers->add($picker);
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
     * @return Game[]
     */
    public function getGames(): array
    {
        $games = [];
        foreach ($this->pickers as $picker) {
            array_push($games, ...$picker->getGames());
        }

        return $games;
    }

    public function getGameIds(): array
    {
        return array_map(function(Game $game) {
            return $game->getId();
        }, $this->getGames());
    }

    public function hasPicks(): bool
    {
        return count($this->getGames()) > 0;
    }

    public function getPickForGame(int $gameId): EventPick
    {
        foreach ($this->pickers as $picker) {
            if ($pick = $picker->findPickOfGame($gameId)) {
                return $pick;
            }
        }

        throw new DomainException(
            sprintf(
                "Participant '%s' doesn't have pick for game '%s'",
                $this->getUser()->getProfileName(),
                $gameId
            )
        );
    }

    public function updatePlaytimeForGame(int $gameId, int $playtime): self
    {
        $this->getPickForGame($gameId)->updatePlaytime($playtime);
        return $this;
    }

    public function updateAchievementsForGame(int $gameId, int $achievements): self
    {
        $this->getPickForGame($gameId)->updateAchievements($achievements);
        return $this;
    }
}

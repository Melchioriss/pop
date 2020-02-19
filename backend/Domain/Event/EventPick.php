<?php

namespace PlayOrPay\Domain\Event;

use PlayOrPay\Domain\Steam\Game;
use Ramsey\Uuid\UuidInterface;

class EventPick
{
    /** @var UuidInterface */
    private $uuid;

    /** @var EventPicker */
    private $picker;

    /** @var Game */
    private $game;

    /** @var EventPickType */
    private $type;

    /** @var EventPickPlayedStatus */
    private $playedStatus;

    /** @var PlayingState */
    private $playingState;

    public function __construct(UuidInterface $uuid, EventPicker $picker, Game $game, EventPickType $type, EventPickPlayedStatus $playedStatus, PlayingState $playingState)
    {
        $this->uuid = $uuid;
        $this->picker = $picker;
        $this->game = $game;
        $this->type = $type;
        $this->playedStatus = $playedStatus;
        $this->playingState = $playingState;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getPicker(): EventPicker
    {
        return $this->picker;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function changeGame(Game $game): self
    {
        if ($game === $this->game) {
            return $this;
        }

        $this->game = $game;

        $this->changePlayedStatus(new EventPickPlayedStatus(EventPickPlayedStatus::NOT_PLAYED));
        $this->clearPlayingState();

        return $this;
    }

    public function getType(): EventPickType
    {
        return $this->type;
    }

    public function getPlayedStatus(): EventPickPlayedStatus
    {
        return $this->playedStatus;
    }

    public function changePlayedStatus(EventPickPlayedStatus $playedStatus): self
    {
        if ((string) $this->playedStatus === (string) $playedStatus) {
            return $this;
        }

        $this->playedStatus = $playedStatus;

        return $this;
    }

    public function getPlayingState(): PlayingState
    {
        return $this->playingState;
    }

    public function updatePlaytime(int $playtime): self
    {
        $this->playingState->updatePlaytime($playtime);

        return $this;
    }

    public function updateAchievements(int $achievements): self
    {
        $this->playingState->updateAchievements($achievements);

        return $this;
    }

    public function clearPlayingState(): self
    {
        $this->playingState->clear();

        return $this;
    }
}

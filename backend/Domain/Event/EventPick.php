<?php

namespace PlayOrPay\Domain\Event;

use DomainException;
use PlayOrPay\Domain\Game\Game;
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

    /** @var EventPickStatus */
    private $status;

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
        $this->status = new EventPickStatus(EventPickStatus::ACTIVE);
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
        if ($this->playedStatus->equalToOneOf(...[
            EventPickPlayedStatus::BEATEN,
            EventPickPlayedStatus::COMPLETED,
        ])) {
            throw new DomainException(sprintf("You can't change game for beaten or completed game, " . "because this was already rewarded and it's ambiguous situation to resolve it automatically"));
        }

        if ($game === $this->game) {
            throw new DomainException(sprintf("This pick is already for game '%s'", $game->getName()));
        }

        $this->game = $game;

        $this
            ->resetPlayedStatus()
            ->clearPlayingState();

        if (!$this->isActive()) {
            $this->activate();
        }

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

    public function changePlayedStatus(EventPickPlayedStatus $newStatus): self
    {
        if ($this->playedStatus->equalTo($newStatus)) {
            throw new DomainException(sprintf("Pick already in status '%s'", (string) $newStatus));
        }

        $this->playedStatus = $newStatus;

        return $this;
    }

    public function resetPlayedStatus(): self
    {
        $this->playedStatus = new EventPickPlayedStatus(EventPickPlayedStatus::NOT_PLAYED);

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

    public function getEvent(): Event
    {
        return $this->getPicker()->getEvent();
    }

    public function getParticipant(): EventParticipant
    {
        return $this->getPicker()->getParticipant();
    }

    public function isCompleted(): bool
    {
        return $this->playedStatus->equalTo(EventPickPlayedStatus::COMPLETED);
    }

    public function isBeaten(bool $strict = true): bool
    {
        return $strict
            ? $this->playedStatus->equalTo(EventPickPlayedStatus::BEATEN)
            : $this->playedStatus->equalToOneOf([
                EventPickPlayedStatus::BEATEN,
                EventPickPlayedStatus::COMPLETED,
            ]);
    }

    public function isActive(): bool
    {
        return $this->status->equalTo(new EventPickStatus(EventPickStatus::ACTIVE));
    }

    public function isRejected(): bool
    {
        return $this->status->equalTo(new EventPickStatus(EventPickStatus::REJECTED));
    }

    private function activate(): self
    {
        if ($this->isActive()) {
            throw new DomainException('Pick is already active');
        }

        $this->status = new EventPickStatus(EventPickStatus::ACTIVE);

        return $this;
    }

    public function reject(): self
    {
        if ($this->isRejected()) {
            throw new DomainException('Pick is already rejected');
        }

        $this->status = new EventPickStatus(EventPickStatus::REJECTED);

        return $this;
    }

    public function getStatus(): EventPickStatus
    {
        return $this->status;
    }
}

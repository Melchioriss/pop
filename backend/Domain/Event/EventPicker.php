<?php

namespace PlayOrPay\Domain\Event;

use Doctrine\Common\Collections\ArrayCollection;
use DomainException;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Domain\Steam\Game;
use PlayOrPay\Domain\User\User;
use Ramsey\Uuid\UuidInterface;

class EventPicker
{
    /** @var UuidInterface */
    private $uuid;

    /** @var EventParticipant */
    private $participant;

    /** @var User */
    private $user;

    /** @var EventPickerType */
    private $type;

    /** @var EventPick[] */
    private $picks;

    /** @var EventPickerComment[] */
    private $comments;

    public function __construct(UuidInterface $uuid, EventParticipant $participant, User $user, EventPickerType $type)
    {
        $this->uuid = $uuid;
        $this->participant = $participant;
        $this->user = $user;
        $this->type = $type;
        $this->picks = new ArrayCollection;
        $this->comments = new ArrayCollection;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getParticipant(): EventParticipant
    {
        return $this->participant;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getType(): EventPickerType
    {
        return $this->type;
    }

    public function replaceUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function findPickByType(EventPickType $type): ?EventPick
    {
        foreach ($this->picks as $pick) {
            if ((string)$pick->getType() === (string)$type) {
                return $pick;
            }
        }

        return null;
    }

    public function findPick(UuidInterface $uuid): ?EventPick
    {
        foreach ($this->picks as $pick) {
            if ($pick->getUuid()->toString() === $uuid->toString()) {
                return $pick;
            }
        }

        return null;
    }

    /**
     * @param UuidInterface $pickUuid
     * @return EventPick
     * @throws NotFoundException
     */
    public function getPick(UuidInterface $pickUuid): EventPick
    {
        $pick = $this->findPick($pickUuid);
        if (!$pick) {
            throw NotFoundException::forObject(EventPick::class, (string)$pickUuid);
        }

        return $pick;
    }

    /**
     * @param EventPickType $type
     * @return EventPick
     * @throws NotFoundException
     */
    public function getPickOfType(EventPickType $type): EventPick
    {
        $pick = $this->findPickByType($type);
        if ($pick) {
            return $pick;
        }

        throw NotFoundException::forObject(EventPick::class, (string)$type);
    }

    public function makePick(UuidInterface $pickUuid, EventPickType $type, Game $game)
    {
        if ($this->findPickByType($type)) {
            throw new DomainException(sprintf("Pick of '%s' type aready exists", (string)$type));
        }

        $pick = new EventPick(
            $pickUuid,
            $this,
            $game,
            $type,
            new EventPickPlayedStatus(EventPickPlayedStatus::NOT_PLAYED),
            new PlayingState
        );

        $this->picks->add($pick);
        return $pick;
    }

    public function getPicks(): array
    {
        return $this->picks;
    }

    /**
     * @return Game[]
     */
    public function getGames(): array
    {
        $games = [];
        foreach ($this->picks as $pick) {
            $games[] = $pick->getGame();
        }

        return $games;
    }

    public function addComment(UuidInterface $uuid, User $user, string $text): self
    {
        $comment = new EventPickerComment($uuid, $this, $user, $text);
        $this->comments->add($comment);
        return $this;
    }

    public function findPickOfGame(int $gameId): ?EventPick
    {
        foreach ($this->picks as $pick) {
            if ($pick->getGame()->getId() === $gameId) {
                return $pick;
            }
        }

        return null;
    }
}

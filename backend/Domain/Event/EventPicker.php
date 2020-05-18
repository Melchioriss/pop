<?php

namespace PlayOrPay\Domain\Event;

use Doctrine\Common\Collections\ArrayCollection;
use DomainException;
use Insideone\Package\EnumFramework\AmbiguousValueException;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Domain\Game\Game;
use PlayOrPay\Domain\Game\GameId;
use PlayOrPay\Domain\Game\StoreId;
use PlayOrPay\Domain\User\User;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

class EventPicker
{
    const PICK_QUOTA = [
        EventPickerType::MINOR => 3,
        EventPickerType::MAJOR => 4,
    ];

    /** @var UuidInterface */
    private $uuid;

    /** @var EventParticipant */
    private $participant;

    /** @var User */
    private $user;

    /** @var EventPickerType */
    private $type;

    /** @var EventPick[]|ArrayCollection<int, EventPick> */
    private $picks;

    /** @var EventPickerComment[]|ArrayCollection<int, EventPickerComment> */
    private $comments;

    public function __construct(UuidInterface $uuid, EventParticipant $participant, User $user, EventPickerType $type)
    {
        $this->uuid = $uuid;
        $this->participant = $participant;
        $this->user = $user;
        $this->type = $type;
        $this->picks = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
            if ((string) $pick->getType() === (string) $type) {
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
     *
     * @throws NotFoundException
     *
     * @return EventPick
     */
    public function getPick(UuidInterface $pickUuid): EventPick
    {
        $pick = $this->findPick($pickUuid);
        if (!$pick) {
            throw NotFoundException::forObject(EventPick::class, $pickUuid->toString());
        }

        return $pick;
    }

    /**
     * @param EventPickType $type
     *
     * @throws NotFoundException
     *
     * @return EventPick
     */
    public function getPickOfType(EventPickType $type): EventPick
    {
        $pick = $this->findPickByType($type);
        if ($pick) {
            return $pick;
        }

        throw NotFoundException::forObject(EventPick::class, (string) $type);
    }

    public function makePick(UuidInterface $pickUuid, EventPickType $type, Game $game): EventPick
    {
        if ($this->getRestPickQuota() === 0) {
            throw new DomainException(sprintf("Picker '%s' has already done their allowed %d picks", $this->getUser()->getProfileName(), $this->getPickQuota()));
        }

        if ($this->findPickByType($type)) {
            throw new DomainException(sprintf("Pick of '%s' type aready exists", (string) $type));
        }

        $pick = new EventPick(
            $pickUuid,
            $this,
            $game,
            $type,
            new EventPickPlayedStatus(EventPickPlayedStatus::NOT_PLAYED),
            new PlayingState()
        );

        $this->picks->add($pick);

        return $pick;
    }

    /**
     * @return EventPick[]
     */
    public function getPicks(): array
    {
        return $this->picks->toArray();
    }

    /**
     * @param StoreId $ofStore
     *
     * @return Game[]
     */
    public function getGames(?StoreId $ofStore = null): array
    {
        $games = [];
        foreach ($this->picks as $pick) {
            if ($ofStore && !$pick->getGame()->getId()->getStoreId()->equalTo($ofStore)) {
                continue;
            }

            $games[] = $pick->getGame();
        }

        return $games;
    }

    /**
     * @param UuidInterface $uuid
     * @param User $user
     * @param string $text
     * @param UuidInterface|null $referencedPickUuid
     * @param EventCommentGameReferenceType|null $gameReferenceType
     *
     * @throws AmbiguousValueException
     * @throws NotFoundException
     * @throws ReflectionException
     *
     * @return EventPickerComment
     */
    public function addComment(
        UuidInterface $uuid,
        User $user,
        string $text,
        ?UuidInterface $referencedPickUuid,
        ?EventCommentGameReferenceType $gameReferenceType
    ): EventPickerComment {
        if ($referencedPickUuid && $gameReferenceType->equalTo(EventCommentGameReferenceType::REVIEW)) {
            $pick = $this->getPick($referencedPickUuid);
            if ($this->hasRewiew($pick->getGame())) {
                throw new DomainException('This game already has a review');
            }
        }

        $comment = new EventPickerComment($uuid, $this, $user, $text, $referencedPickUuid, $gameReferenceType);
        $this->comments->add($comment);

        return $comment;
    }

    public function hasRewiew(Game $game): bool
    {
        foreach ($this->comments as $comment) {
            if ($comment->isReviewFor($game)) {
                return true;
            }
        }

        return false;
    }

    public function findPickOfGame(GameId $gameId): ?EventPick
    {
        foreach ($this->picks as $pick) {
            if ($pick->getGame()->getId()->equalTo($gameId)) {
                return $pick;
            }
        }

        return null;
    }

    /**
     * @param GameId $gameId
     *
     * @throws NotFoundException
     *
     * @return EventPick
     */
    public function getPickOfGame(GameId $gameId): EventPick
    {
        $pick = $this->findPickOfGame($gameId);
        if (!$pick) {
            throw NotFoundException::forObject(Game::class, $gameId);
        }

        return $pick;
    }

    public function getEvent(): Event
    {
        return $this->getParticipant()->getEvent();
    }

    public function getPickQuota(): int
    {
        return self::PICK_QUOTA[(int) (string) $this->type];
    }

    public function getRestPickQuota(): int
    {
        return $this->getPickQuota() - $this->picks->count();
    }

    public function hasDoneAllPicks(): bool
    {
        return $this->getRestPickQuota() === 0;
    }

    /**
     * @return EventPickerComment[]
     */
    public function getComments(): array
    {
        return $this->comments->toArray();
    }
}

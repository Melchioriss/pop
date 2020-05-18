<?php

namespace PlayOrPay\Domain\Event;

use Assert\Assert;
use DateTimeImmutable;
use DomainException;
use Insideone\Package\EnumFramework\AmbiguousValueException;
use PlayOrPay\Domain\Contracts\Entity\OnUpdateEventListenerInterface;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Domain\Game\Game;
use PlayOrPay\Domain\User\User;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

class EventPickerComment implements OnUpdateEventListenerInterface
{
    const ALLOWED_REPICK_PLAY_STATUSES = [
        EventPickPlayedStatus::NOT_PLAYED,
        EventPickPlayedStatus::UNFINISHED,
    ];

    /** @var UuidInterface */
    private $uuid;

    /** @var EventPicker */
    private $picker;

    /** @var User */
    private $user;

    /** @var string */
    private $text;

    /** @var DateTimeImmutable */
    private $createdAt;

    /** @var EventCommentGameReferenceType|null */
    private $gameReferenceType;

    /** @var Game|null */
    private $referencedGame;

    /** @var string[] */
    private $history = [];

    /** @var DateTimeImmutable|null */
    private $updatedAt;

    /**
     * @param UuidInterface $uuid
     * @param EventPicker $picker
     * @param User $user
     * @param string $text
     * @param UuidInterface|null $referencedPickUuid
     * @param EventCommentGameReferenceType|null $gameReferenceType
     *
     * @throws AmbiguousValueException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function __construct(
        UuidInterface $uuid,
        EventPicker $picker,
        User $user,
        string $text,
        ?UuidInterface $referencedPickUuid,
        ?EventCommentGameReferenceType $gameReferenceType
    ) {
        if ($referencedPickUuid) {
            Assert::that($gameReferenceType)->notNull('Game reference type is required to make a comment');

            $referencedPick = $picker->getPick($referencedPickUuid);
            $playedStatus = $referencedPick->getPlayedStatus();

            if ($gameReferenceType->equalTo(EventCommentGameReferenceType::REVIEW)
                && $playedStatus->equalTo(EventPickPlayedStatus::NOT_PLAYED)
            ) {
                throw new DomainException(sprintf("You can't review '%s' game", $referencedPick->getPlayedStatus()->getCodename()));
            }

            if ($gameReferenceType->equalTo(EventCommentGameReferenceType::REPICK)
                && !$playedStatus->equalToOneOf(self::ALLOWED_REPICK_PLAY_STATUSES)
            ) {
                throw new DomainException(sprintf("You can't request a repick for '%s' played status. Played status must be one of these: %s", $playedStatus->getCodename(), implode(', ', array_map(function (int $rawPlayedStatus) { return (new EventPickPlayedStatus($rawPlayedStatus))->getCodename(); }, self::ALLOWED_REPICK_PLAY_STATUSES))));
            }

            $this->referencedGame = $referencedPick->getGame();
            $this->gameReferenceType = $gameReferenceType;
        }

        $this->uuid = $uuid;
        $this->picker = $picker;
        $this->user = $user;
        $this->text = $text;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getGameReferenceType(): ?EventCommentGameReferenceType
    {
        return $this->gameReferenceType;
    }

    public function getReferencedGame(): ?Game
    {
        return $this->referencedGame;
    }

    public function getEvent(): Event
    {
        return $this->picker->getEvent();
    }

    public function updateText(string $text): void
    {
        $this->history[] = $this->text;
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @return string[]
     */
    public function getHistory(): array
    {
        return $this->history;
    }

    public function getPicker(): EventPicker
    {
        return $this->picker;
    }

    /**
     * @throws NotFoundException
     *
     * @return EventPick
     */
    public function getPick(): EventPick
    {
        $pick = $this->findPick();
        if (!$pick) {
            throw NotFoundException::forQuery(EventPick::class, ['comment' => $this->uuid->toString()]);
        }

        return $pick;
    }

    public function findPick(): ?EventPick
    {
        if (!$this->referencedGame) {
            return null;
        }

        $picker = $this->getPicker();
        $pick = $picker->findPickOfGame($this->referencedGame->getId());

        return $pick ? $pick : null;
    }

    public function onUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isReview(): bool
    {
        return $this->gameReferenceType
            && $this->gameReferenceType->equalTo(
                new EventCommentGameReferenceType(EventCommentGameReferenceType::REVIEW)
            );
    }

    public function isReviewFor(Game $game): bool
    {
        return $this->isReview() && $this->referencedGame === $game;
    }

    public function hasReferencedGame(): bool
    {
        return (bool) $this->referencedGame;
    }
}

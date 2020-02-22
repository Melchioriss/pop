<?php

namespace PlayOrPay\Domain\Event;

use DateTimeImmutable;
use DomainException;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Domain\Steam\Game;
use PlayOrPay\Domain\User\User;
use PlayOrPay\Package\EnumFramework\AmbiguousValueException;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

class EventPickerComment
{
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

    /** @var Game|null */
    private $reviewedGame;

    /**
     * @param UuidInterface $uuid
     * @param EventPicker $picker
     * @param User $user
     * @param string $text
     * @param UuidInterface|null $reviewedPickUuid
     *
     * @throws AmbiguousValueException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function __construct(UuidInterface $uuid, EventPicker $picker, User $user, string $text, ?UuidInterface $reviewedPickUuid)
    {
        if ($reviewedPickUuid) {
            $reviewedPick = $picker->getPick($reviewedPickUuid);
            if ($reviewedPick->getPlayedStatus()->equalToOneOf([
                EventPickPlayedStatus::UNFINISHED,
                EventPickPlayedStatus::NOT_PLAYED,
            ])) {
                throw new DomainException(sprintf("You can't review '%s' game", $reviewedPick->getPlayedStatus()->getCodename()));
            }

            $this->reviewedGame = $reviewedPick->getGame();
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

    public function getReviewedGame(): ?Game
    {
        return $this->reviewedGame;
    }
}

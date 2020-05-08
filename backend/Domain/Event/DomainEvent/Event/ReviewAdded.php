<?php

namespace PlayOrPay\Domain\Event\DomainEvent\Event;

use Assert\Assert;
use PlayOrPay\Domain\Contracts\DomainEvent\DomainEventInterface;
use PlayOrPay\Domain\Event\Event;
use PlayOrPay\Domain\Event\EventPick;
use PlayOrPay\Domain\Event\EventPickerComment;
use PlayOrPay\Domain\Game\Game;
use PlayOrPay\Domain\User\User;

class ReviewAdded implements DomainEventInterface
{
    /** @var EventPickerComment */
    public $comment;

    public function __construct(EventPickerComment $comment)
    {
        Assert::that($comment->isReview())->true('ReviewAdded event accepts only review comments');

        $this->comment = $comment;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize()
    {
        $game = $this->comment->getReferencedGame();
        $pick = $this->comment->findPick();

        return [
            'event'   => $this->comment->getEvent()->getUuid()->toString(),
            'pick'    => $pick ? $pick->getUuid()->toString() : null,
            'comment' => $this->comment->getUuid()->toString(),
            'user'    => (string) $this->comment->getUser()->getSteamId(),
            'game'    => $game ? (string) $game->getId() : null,
        ];
    }

    public static function refsMap(): array
    {
        return [
            'event'   => Event::class,
            'pick'    => EventPick::class,
            'comment' => EventPickerComment::class,
            'user'    => User::class,
            'game'    => Game::class,
        ];
    }
}

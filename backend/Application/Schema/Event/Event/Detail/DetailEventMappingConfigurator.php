<?php

namespace PlayOrPay\Application\Schema\Event\Event\Detail;

use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\MappingOperation\Operation;
use PlayOrPay\Domain\Event\Event;
use PlayOrPay\Domain\Event\EventParticipant;
use PlayOrPay\Domain\Event\EventPick;
use PlayOrPay\Domain\Event\EventPicker;
use PlayOrPay\Domain\Event\EventPickerComment;
use PlayOrPay\Domain\Steam\Game;
use PlayOrPay\Domain\User\User;

class DetailEventMappingConfigurator implements AutoMapperConfiguratorInterface
{
    public function configure(AutoMapperConfigInterface $config): void
    {
        $config
            ->registerMapping(Event::class, DetailEventView::class)
            ->forMember('users', function (Event $event, AutoMapperInterface $mapper) {
                return $mapper->mapMultiple($event->getUsers(), DetailEventUserView::class);
            })
            ->forMember('participants', Operation::mapCollectionTo(DetailEventParticipantView::class));

        $config
            ->registerMapping(EventParticipant::class, DetailEventParticipantView::class)
            ->forMember('user', function (EventParticipant $participant) {
                return (string) $participant->getUser()->getSteamId();
            })
            ->forMember('pickers', Operation::mapCollectionTo(DetailEventPickerView::class));

        $config
            ->registerMapping(EventPicker::class, DetailEventPickerView::class)
            ->forMember('user', function (EventPicker $picker) {
                return (string) $picker->getUser()->getSteamId();
            })
            ->forMember('type', function (EventPicker $picker) {
                return (int) (string) $picker->getType();
            })
            ->forMember('picks', Operation::mapCollectionTo(DetailEventPickView::class))
            ->forMember('comments', Operation::mapCollectionTo(DetailEventPickerComment::class));

        $config
            ->registerMapping(User::class, DetailEventUserView::class)
            ->forMember('steamId', function (User $user) {
                return (string) $user->getSteamId();
            });

        $config
            ->registerMapping(EventPick::class, DetailEventPickView::class)
            ->forMember('type', function (EventPick $pick) {
                return (int) (string) $pick->getType();
            })
            ->forMember('game', function (EventPick $pick, AutoMapperInterface $mapper) {
                return $mapper->map($pick->getGame(), DetailGameView::class);
            })
            ->forMember('playedStatus', function (EventPick $pick) {
                return (int) (string) $pick->getPlayedStatus();
            });

        $config
            ->registerMapping(Game::class, DetailGameView::class);

        $config
            ->registerMapping(EventPickerComment::class, DetailEventPickerComment::class)
            ->forMember('user', function (EventPickerComment $comment) {
                return (string) $comment->getUser()->getSteamId();
            })
            ->forMember('reviewedGame', function (EventPickerComment $comment) {
                $game = $comment->getReviewedGame();

                return $game ? $game->getId() : null;
            });
    }
}

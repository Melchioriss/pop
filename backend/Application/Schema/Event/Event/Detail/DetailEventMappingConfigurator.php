<?php

namespace PlayOrPay\Application\Schema\Event\Event\Detail;

use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\MappingOperation\Operation;
use PlayOrPay\Domain\Event\Event;
use PlayOrPay\Domain\Event\EventEarnedReward;
use PlayOrPay\Domain\Event\EventParticipant;
use PlayOrPay\Domain\Event\EventPick;
use PlayOrPay\Domain\Event\EventPicker;
use PlayOrPay\Domain\Event\EventPickerComment;
use PlayOrPay\Domain\Event\EventReward;
use PlayOrPay\Domain\Game\Game;
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
            ->forMember('pickers', Operation::mapCollectionTo(DetailEventPickerView::class))
            ->forMember('rewards', function (EventParticipant $participant, AutoMapperInterface $mapper) {
                return $mapper->mapMultiple($participant->getRewards(), DetailEventEarnedReward::class);
            })
            ->forMember('totalRewardValue', function (EventParticipant $participant) {
                $rewards = $participant->getRewards();
                $totalRewardValue = 0;
                foreach ($rewards as $reward) {
                    $totalRewardValue += $reward->getValue();
                }

                return $totalRewardValue;
            });

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
            ->registerMapping(EventPickerComment::class, DetailEventPickerComment::class)
            ->forMember('user', function (EventPickerComment $comment) {
                return (string) $comment->getUser()->getSteamId();
            })
            ->forMember('gameReferenceType', function (EventPickerComment $comment) {
                $referenceType = $comment->getGameReferenceType();

                return $referenceType ? (int) (string) $referenceType : null;
            })
            ->forMember('referencedGame', function (EventPickerComment $comment) {
                $referencedGame = $comment->getReferencedGame();

                return $referencedGame ? (string) $referencedGame->getId() : null;
            })
            ->forMember('referencedPick', function (EventPickerComment $comment) {
                $pick = $comment->findPick();

                return $pick ? $pick->getUuid()->toString() : null;
            });

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
            ->forMember('game', function (EventPick $pick) {
                return (string) $pick->getGame()->getId();
            })
            ->forMember('playedStatus', function (EventPick $pick) {
                return (int) (string) $pick->getPlayedStatus();
            })
            ->forMember('rejected', function (EventPick $pick) {
                return $pick->isRejected();
            });

        $config
            ->registerMapping(Game::class, DetailGameView::class)
            ->forMember('id', function (Game $game) {
                return (string) $game->getId();
            })
            ->forMember('storeId', function (Game $game) {
                return (int) (string) $game->getId()->getStoreId();
            })
            ->forMember('localId', function (Game $game) {
                return (string) $game->getId()->getLocalId();
            });

        $config
            ->registerMapping(EventEarnedReward::class, DetailEventEarnedReward::class)
            ->forMember('pick', function (EventEarnedReward $reward) {
                $pick = $reward->getPick();

                return $pick ? $pick->getUuid()->toString() : null;
            })
            ->forMember('reason', function (EventEarnedReward $reward) {
                return (int) (string) $reward->getReason();
            });

        $config->registerMapping(EventReward::class, DetailEventReward::class)
            ->forMember('reason', function (EventReward $reward) {
                return (int) (string) $reward->getReason();
            });
    }
}

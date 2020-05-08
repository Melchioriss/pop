<?php

namespace PlayOrPay\Application\Schema\DomainEvent\Activity;

use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use PlayOrPay\Domain\DomainEvent\DomainEventRecord;
use PlayOrPay\Domain\Event\EventPick;
use PlayOrPay\Domain\Event\EventPickerComment;
use PlayOrPay\Domain\Game\Game;
use PlayOrPay\Domain\Steam\Group;
use PlayOrPay\Domain\User\User;
use ReflectionClass;

class CollectionDomainEventRecordMappingConfigurator implements AutoMapperConfiguratorInterface
{
    public function configure(AutoMapperConfigInterface $config): void
    {
        $config->registerMapping(DomainEventRecord::class, CollectionDomainEventRecord::class)
            ->forMember('name', function (DomainEventRecord $record) {
                return (new ReflectionClass($record->getName()))->getShortName();
            })
            ->forMember('actor', function (DomainEventRecord $record) {
                return (string) $record->getActor()->getSteamId();
            });

        $config->registerMapping(Game::class, ActivityGame::class)
            ->forMember('id', function (Game $game) {
                return (string) $game->getId();
            })
            ->forMember('localId', function (Game $game) {
                return $game->getId()->getLocalId();
            });

        $config->registerMapping(EventPick::class, ActivityPick::class)
            ->forMember('playedStatus', function (EventPick $pick) {
                return (int) (string) $pick->getPlayedStatus();
            })
            ->forMember('type', function (EventPick $pick) {
                return (int) (string) $pick->getType();
            });

        $config->registerMapping(User::class, ActivityUser::class)
            ->forMember('steamId', function (User $user) {
                return (string) $user->getSteamId();
            });

        $config->registerMapping(Group::class, ActivityGroup::class)
            ->forMember('id', function (Group $group) {
                return (string) $group->getId();
            });

        $config->registerMapping(EventPickerComment::class, ActivityComment::class);
    }
}

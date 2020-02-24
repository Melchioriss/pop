<?php

namespace PlayOrPay\Application\Schema\DomainEvent\Log;

use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use PlayOrPay\Domain\DomainEvent\DomainEventRecord;
use PlayOrPay\Domain\Event\EventPick;
use PlayOrPay\Domain\Game\Game;
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

        $config->registerMapping(Game::class, LogGame::class)
            ->forMember('id', function (Game $game) {
                return (string) $game->getId();
            });

        $config->registerMapping(EventPick::class, LogPick::class)
            ->forMember('playedStatus', function (EventPick $pick) {
                return (int)(string) $pick->getPlayedStatus();
            })
            ->forMember('type', function (EventPick $pick) {
                return (int)(string) $pick->getType();
            });

        $config->registerMapping(User::class, LogUser::class)
            ->forMember('steamId', function (User $user) {
                return (string) $user->getSteamId();
            });
    }
}

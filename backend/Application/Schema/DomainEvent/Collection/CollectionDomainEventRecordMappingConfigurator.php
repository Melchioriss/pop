<?php

namespace PlayOrPay\Application\Schema\DomainEvent\Collection;

use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use PlayOrPay\Domain\DomainEvent\DomainEventRecord;
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
    }
}

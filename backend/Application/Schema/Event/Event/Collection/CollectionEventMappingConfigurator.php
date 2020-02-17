<?php

namespace PlayOrPay\Application\Schema\Event\Event\Collection;

use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use PlayOrPay\Domain\Event\Event;

class CollectionEventMappingConfigurator implements AutoMapperConfiguratorInterface
{
    public function configure(AutoMapperConfigInterface $config): void
    {
        $config
            ->registerMapping(Event::class, CollectionEventView::class);
    }
}

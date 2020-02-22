<?php

namespace PlayOrPay\Application\Schema\Steam\Group\Common;

use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use PlayOrPay\Domain\Steam\Group;

class CommonGroupMappingConfigurator implements AutoMapperConfiguratorInterface
{
    public function configure(AutoMapperConfigInterface $config): void
    {
        $config
            ->registerMapping(Group::class, GroupView::class)
            ->forMember('id', function (Group $group) {
                return (string) $group->getId();
            });
    }
}

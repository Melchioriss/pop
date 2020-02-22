<?php

namespace PlayOrPay\Application\Schema\User\User\Common;

use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use PlayOrPay\Domain\User\User;

class CommonUserMappingConfigurator implements AutoMapperConfiguratorInterface
{
    public function configure(AutoMapperConfigInterface $config): void
    {
        $config
            ->registerMapping(User::class, CommonUserView::class)
            ->forMember('steamId', function (User $user) {
                return (string) $user->getSteamId();
            })
            ->forMember('roles', function (User $user) {
                return $user->getRoleNames();
            })
            ->forMember('admin', function (User $user) {
                return $user->isAdmin();
            });
    }
}

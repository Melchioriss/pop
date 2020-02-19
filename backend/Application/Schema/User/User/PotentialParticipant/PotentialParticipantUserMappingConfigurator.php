<?php

namespace PlayOrPay\Application\Schema\User\User\PotentialParticipant;

use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\MappingOperation\Operation;
use PlayOrPay\Domain\User\User;

class PotentialParticipantUserMappingConfigurator implements AutoMapperConfiguratorInterface
{
    public function configure(AutoMapperConfigInterface $config): void
    {
        $config->registerMapping(User::class, PotentialParticipantUserView::class)
            ->forMember('steamId', Operation::mapFrom(function (User $user) {
                return (string) $user->getSteamId();
            }));
    }
}

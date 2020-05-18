<?php

namespace PlayOrPay\Application\Schema\Game\Game\Common;

use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use PlayOrPay\Domain\Game\Game;

class CommonGameMappingConfigurator implements AutoMapperConfiguratorInterface
{
    public function configure(AutoMapperConfigInterface $config): void
    {
        $config
            ->registerMapping(Game::class, GameView::class)
            ->forMember('id', function (Game $game) {
                return (string) $game->getId();
            })
            ->forMember('storeId', function (Game $game) {
                return (int) (string) $game->getId()->getStoreId();
            })
            ->forMember('localId', function (Game $game) {
                return (string) $game->getId()->getLocalId();
            });
    }
}

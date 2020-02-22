<?php

namespace PlayOrPay\Application\Schema\Steam\Game\Common;

use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use PlayOrPay\Domain\Steam\Game;

class CommonGameMappingConfigurator implements AutoMapperConfiguratorInterface
{
    public function configure(AutoMapperConfigInterface $config): void
    {
        $config
            ->registerMapping(Game::class, GameView::class)
            ->forMember('id', function (Game $game) {
                return (string) $game->getId();
            });
    }
}

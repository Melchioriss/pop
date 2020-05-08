<?php

namespace PlayOrPay\Security\Game\Game;

use PlayOrPay\Application\Command\Steam\Game\ImportSteamGamesCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class ImportGamesSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param ImportSteamGamesCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(ImportSteamGamesCommand $command): void
    {
        $this->assertAdmin();
    }
}

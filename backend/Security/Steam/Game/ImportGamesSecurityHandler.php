<?php

namespace PlayOrPay\Security\Steam\Game;

use PlayOrPay\Application\Command\Steam\Game\ImportGamesCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class ImportGamesSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param ImportGamesCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(ImportGamesCommand $command)
    {
        $this->assertAdmin();
    }
}

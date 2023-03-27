<?php

declare(strict_types=1);

namespace PlayOrPay\Security\Game\Game;

use PlayOrPay\Application\Command\Steam\Game\ImportSteamGameCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class ImportGameSecurityHandler extends CommonSecurityHandler
{
    /**
     * @throws SecuriryException
     */
    public function __invoke(ImportSteamGameCommand $command): void
    {
        $this->assertAdmin();
    }
}

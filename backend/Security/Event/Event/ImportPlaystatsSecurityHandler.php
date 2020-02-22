<?php

namespace PlayOrPay\Security\Event\Event;

use PlayOrPay\Application\Command\Event\Event\ImportSteamPlaystats\ImportSteamPlayingStatesCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class ImportPlaystatsSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param ImportSteamPlayingStatesCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(ImportSteamPlayingStatesCommand $command)
    {
        $this->assertAdmin();
    }
}

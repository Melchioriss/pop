<?php

namespace PlayOrPay\Security\Event\Event;

use PlayOrPay\Application\Command\Event\Event\ImportPlaystats\ImportPlayingStatesCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class ImportPlaystatsSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param ImportPlayingStatesCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(ImportPlayingStatesCommand $command)
    {
        $this->assertAdmin();
    }
}

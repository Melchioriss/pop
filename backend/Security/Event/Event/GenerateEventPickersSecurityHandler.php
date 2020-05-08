<?php

namespace PlayOrPay\Security\Event\Event;

use PlayOrPay\Application\Command\Event\Event\GeneratePickers\GenerateEventPickersCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class GenerateEventPickersSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param GenerateEventPickersCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(GenerateEventPickersCommand $command): void
    {
        $this->assertAdmin();
    }
}

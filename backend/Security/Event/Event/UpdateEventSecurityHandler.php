<?php

namespace PlayOrPay\Security\Event\Event;

use PlayOrPay\Application\Command\Event\Event\Update\UpdateEventCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class UpdateEventSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param UpdateEventCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(UpdateEventCommand $command): void
    {
        $this->assertAdmin();
    }
}

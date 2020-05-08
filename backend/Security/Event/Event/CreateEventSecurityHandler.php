<?php

namespace PlayOrPay\Security\Event\Event;

use PlayOrPay\Application\Command\Event\Event\Create\CreateEventCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class CreateEventSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param CreateEventCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(CreateEventCommand $command): void
    {
        $this->assertAdmin();
    }
}

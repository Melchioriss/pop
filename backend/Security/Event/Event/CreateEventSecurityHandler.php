<?php

namespace PlayOrPay\Security\Event\Event;

use PlayOrPay\Application\Command\Event\Event\Create\CreateEventCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class CreateEventSecurityHandler extends CommonSecurityHandler
{
    /**
     * @throws SecuriryException
     */
    public function __invoke(CreateEventCommand $command)
    {
        $this->assertAdmin();
    }
}

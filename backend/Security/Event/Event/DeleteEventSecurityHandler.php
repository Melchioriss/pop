<?php

namespace PlayOrPay\Security\Event\Event;

use PlayOrPay\Application\Command\Event\Event\Delete\DeleteEventCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class DeleteEventSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param DeleteEventCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(DeleteEventCommand $command): void
    {
        $this->assertAdmin();
    }
}

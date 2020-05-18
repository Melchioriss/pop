<?php

namespace PlayOrPay\Security\Event\Event;

use PlayOrPay\Application\Command\Event\Event\AddParticipant\AddEventParticipantCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class AddEventParticipantSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param AddEventParticipantCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(AddEventParticipantCommand $command): void
    {
        $this->assertAdmin();
    }
}

<?php

namespace PlayOrPay\Security\Event\Event;

use PlayOrPay\Application\Command\Event\EventParticipant\Delete\DeleteEventParticipantCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class DeleteEventParticipantSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param DeleteEventParticipantCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(DeleteEventParticipantCommand $command): void
    {
        $this->assertAdmin();
    }
}

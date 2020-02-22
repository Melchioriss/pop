<?php

namespace PlayOrPay\Security\Event\EventParticipant;

use PlayOrPay\Application\Command\Event\EventParticipant\UpdateBlaeoPoints\UpdateEventParticipantBlaeoPointsCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class UpdateEventParticipantBlaeoPointsSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param UpdateEventParticipantBlaeoPointsCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(UpdateEventParticipantBlaeoPointsCommand $command): void
    {
        $this->assertAdmin();
    }
}

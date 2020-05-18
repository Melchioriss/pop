<?php

namespace PlayOrPay\Security\Event\EventParticipant;

use PlayOrPay\Application\Command\Event\EventParticipant\AddPicker\AddEventParticipantPickerCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class AddEventParticipantPickerSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param AddEventParticipantPickerCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(AddEventParticipantPickerCommand $command): void
    {
        $this->assertAdmin();
    }
}

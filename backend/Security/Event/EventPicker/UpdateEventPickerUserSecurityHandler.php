<?php

namespace PlayOrPay\Security\Event\EventPicker;

use PlayOrPay\Application\Command\Event\EventPicker\UpdateUser\UpdateEventPickerUserCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class UpdateEventPickerUserSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param UpdateEventPickerUserCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(UpdateEventPickerUserCommand $command): void
    {
        $this->assertAdmin();
    }
}

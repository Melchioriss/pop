<?php

namespace PlayOrPay\Security\User\User;

use PlayOrPay\Application\Command\User\User\Activate\ActivateUserCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class ActivateUserSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param ActivateUserCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(ActivateUserCommand $command)
    {
        $this->assertAdmin();
    }
}

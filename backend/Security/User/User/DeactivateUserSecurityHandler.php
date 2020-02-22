<?php

namespace PlayOrPay\Security\User\User;

use PlayOrPay\Application\Command\User\User\Deactivate\DeactivateUserCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class DeactivateUserSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param DeactivateUserCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(DeactivateUserCommand $command)
    {
        $this->assertAdmin();
    }
}

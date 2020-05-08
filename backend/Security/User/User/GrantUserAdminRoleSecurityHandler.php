<?php

namespace PlayOrPay\Security\User\User;

use PlayOrPay\Application\Command\User\User\GrantAdminRole\GrantUserAdminRoleCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class GrantUserAdminRoleSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param GrantUserAdminRoleCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(GrantUserAdminRoleCommand $command): void
    {
        $this->assertAdmin();
    }
}

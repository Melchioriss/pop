<?php

namespace PlayOrPay\Security\User\User;

use PlayOrPay\Application\Command\User\User\RevokeAdminRole\RevokeUserAdminRoleCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class RevokeUserAdminRoleSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param RevokeUserAdminRoleCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(RevokeUserAdminRoleCommand $command): void
    {
        $this->assertAdmin();
    }
}

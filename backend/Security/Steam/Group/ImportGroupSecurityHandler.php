<?php

namespace PlayOrPay\Security\Steam\Group;

use PlayOrPay\Application\Command\Steam\Group\Import\ImportGroupCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class ImportGroupSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param ImportGroupCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(ImportGroupCommand $command): void
    {
        $this->assertAdmin();
    }
}

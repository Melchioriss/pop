<?php

namespace PlayOrPay\Security\Content\Block;

use PlayOrPay\Application\Command\Content\Block\PutBlockCommand;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class PutBlockSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param PutBlockCommand $command
     *
     * @throws SecuriryException
     */
    public function __invoke(PutBlockCommand $command): void
    {
        $this->assertAdmin();
    }
}

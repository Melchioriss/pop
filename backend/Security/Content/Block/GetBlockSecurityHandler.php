<?php

namespace PlayOrPay\Security\Content\Block;

use PlayOrPay\Application\Query\Content\Block\GetBlockQuery;
use PlayOrPay\Security\SecurityHandlerInterface;

class GetBlockSecurityHandler implements SecurityHandlerInterface
{
    public function __invoke(GetBlockQuery $query): void
    {
        // for everyone
    }
}

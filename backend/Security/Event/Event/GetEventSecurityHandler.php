<?php

namespace PlayOrPay\Security\Event\Event;

use PlayOrPay\Application\Query\Event\Event\Get\GetEventQuery;
use PlayOrPay\Security\CommonSecurityHandler;

class GetEventSecurityHandler extends CommonSecurityHandler
{
    public function __invoke(GetEventQuery $query): void
    {
        // access for everyone
    }
}

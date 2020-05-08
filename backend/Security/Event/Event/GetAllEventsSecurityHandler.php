<?php

namespace PlayOrPay\Security\Event\Event;

use PlayOrPay\Application\Query\Event\Event\GetAll\GetAllEventsQuery;
use PlayOrPay\Security\CommonSecurityHandler;

class GetAllEventsSecurityHandler extends CommonSecurityHandler
{
    public function __invoke(GetAllEventsQuery $query): void
    {
        // access for everyone
    }
}

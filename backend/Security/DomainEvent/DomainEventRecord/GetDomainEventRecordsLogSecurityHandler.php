<?php

namespace PlayOrPay\Security\DomainEvent\DomainEventRecord;

use PlayOrPay\Application\Query\DomainEvent\DomainEventRecord\GetLog\GetDomainEventRecordsLogQuery;
use PlayOrPay\Security\CommonSecurityHandler;

class GetDomainEventRecordsLogSecurityHandler extends CommonSecurityHandler
{
    public function __invoke(GetDomainEventRecordsLogQuery $query)
    {
        // for everyone
    }
}

<?php

namespace PlayOrPay\Security\DomainEvent\DomainEventRecord;

use PlayOrPay\Application\Query\DomainEvent\DomainEventRecord\GetActivity\GetDomainEventRecordsActivityQuery;
use PlayOrPay\Security\CommonSecurityHandler;

class GetDomainEventRecordsActivitySecurityHandler extends CommonSecurityHandler
{
    public function __invoke(GetDomainEventRecordsActivityQuery $query): void
    {
        // for everyone
    }
}

<?php

namespace PlayOrPay\Security\DomainEvent\DomainEventRecord;

use PlayOrPay\Application\Query\DomainEvent\DomainEventRecord\GetAll\GetAllDomainEventRecordsQuery;
use PlayOrPay\Security\CommonSecurityHandler;

class GetAllDomainEventRecordsSecurityHandler extends CommonSecurityHandler
{
    public function __invoke(GetAllDomainEventRecordsQuery $query)
    {
        // for everyone
    }
}

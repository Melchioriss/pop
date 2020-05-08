<?php

namespace PlayOrPay\Security\Steam\Group;

use PlayOrPay\Application\Query\Steam\Group\GetAll\GetAllGroupsQuery;
use PlayOrPay\Security\CommonSecurityHandler;

class GetAllGroupsSecurityHandler extends CommonSecurityHandler
{
    public function __invoke(GetAllGroupsQuery $query): void
    {
        // access for everyone
    }
}

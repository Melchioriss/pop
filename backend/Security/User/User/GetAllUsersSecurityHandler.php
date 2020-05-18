<?php

namespace PlayOrPay\Security\User\User;

use PlayOrPay\Application\Query\User\User\GetAll\GetAllUsersQuery;
use PlayOrPay\Security\CommonSecurityHandler;

class GetAllUsersSecurityHandler extends CommonSecurityHandler
{
    public function __invoke(GetAllUsersQuery $query): void
    {
        // access for everyone
    }
}

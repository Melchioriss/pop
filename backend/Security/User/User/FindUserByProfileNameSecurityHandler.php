<?php

namespace PlayOrPay\Security\User\User;

use PlayOrPay\Application\Query\User\User\FindByProfileName\FindUserByProfileNameQuery;
use PlayOrPay\Security\CommonSecurityHandler;

class FindUserByProfileNameSecurityHandler extends CommonSecurityHandler
{
    public function __invoke(FindUserByProfileNameQuery $query): void
    {
        // access for everyone
    }
}

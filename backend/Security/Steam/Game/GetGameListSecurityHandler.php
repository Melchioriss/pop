<?php

namespace PlayOrPay\Security\Steam\Game;

use PlayOrPay\Application\Query\SearchPaginatedQuery;
use PlayOrPay\Security\CommonSecurityHandler;

class GetGameListSecurityHandler extends CommonSecurityHandler
{
    public function __invoke(SearchPaginatedQuery $query)
    {
        // access for everyone
    }
}

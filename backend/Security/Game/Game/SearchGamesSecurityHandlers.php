<?php

namespace PlayOrPay\Security\Game\Game;

use PlayOrPay\Application\Query\Game\Game\Search\SearchGamesQuery;
use PlayOrPay\Security\CommonSecurityHandler;

class SearchGamesSecurityHandlers extends CommonSecurityHandler
{
    public function __invoke(SearchGamesQuery $query): void
    {
        // access for everyone
    }
}

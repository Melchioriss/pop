<?php

namespace PlayOrPay\Security\Event\Event;

use PlayOrPay\Application\Query\Event\Event\GetPotentialParticipants\GetEventPotentialParticipantsQuery;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class GetEventPotentialParticipantsSecurityHandler extends CommonSecurityHandler
{
    /**
     * @param GetEventPotentialParticipantsQuery $query
     *
     * @throws SecuriryException
     */
    public function __invoke(GetEventPotentialParticipantsQuery $query): void
    {
        $this->assertAdmin();
    }
}

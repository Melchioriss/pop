<?php

namespace PlayOrPay\Security\Event\Event;

use Doctrine\ORM\EntityNotFoundException;
use PlayOrPay\Application\Query\Event\Event\GetPotentialParticipants\GetEventPotentialParticipantsQuery;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class GetEventPotentialParticipantsSecurityHandler extends CommonSecurityHandler
{

    /**
     * @param GetEventPotentialParticipantsQuery $query
     *
     * @throws SecuriryException
     */
    public function __invoke(GetEventPotentialParticipantsQuery $query)
    {
        $this->assertAdmin();
    }
}

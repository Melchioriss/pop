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
    /** @var EventRepository */
    private $eventRepo;

    public function __construct(ActorFinder $actorFinder, EventRepository $eventRepo)
    {
        parent::__construct($actorFinder);
        $this->eventRepo = $eventRepo;
    }

    /**
     * @param GetEventPotentialParticipantsQuery $query
     *
     * @throws EntityNotFoundException
     * @throws SecuriryException
     */
    public function __invoke(GetEventPotentialParticipantsQuery $query)
    {
        $event = $this->eventRepo->get($query->eventUuid);
        $this->assertBeingInGroup($event->getGroup());
    }
}

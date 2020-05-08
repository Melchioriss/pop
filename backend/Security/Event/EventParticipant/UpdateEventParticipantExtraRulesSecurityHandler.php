<?php

namespace PlayOrPay\Security\Event\EventParticipant;

use Doctrine\ORM\EntityNotFoundException;
use PlayOrPay\Application\Command\Event\EventParticipant\UpdateExtraRules\UpdateEventParticipantExtraRulesCommand;
use PlayOrPay\Infrastructure\Storage\Event\EventParticipantRepository;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class UpdateEventParticipantExtraRulesSecurityHandler extends CommonSecurityHandler
{
    /** @var EventParticipantRepository */
    private $participantRepo;

    public function __construct(ActorFinder $actorFinder, EventParticipantRepository $participantRepo)
    {
        parent::__construct($actorFinder);
        $this->participantRepo = $participantRepo;
    }

    /**
     * @param UpdateEventParticipantExtraRulesCommand $command
     *
     * @throws EntityNotFoundException
     * @throws SecuriryException
     */
    public function __invoke(UpdateEventParticipantExtraRulesCommand $command): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $participant = $this->participantRepo->get($command->getParticipantUuid());
        $this->assertActor($participant->getUser());
    }
}

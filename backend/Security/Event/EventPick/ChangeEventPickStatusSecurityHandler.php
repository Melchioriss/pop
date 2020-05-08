<?php

namespace PlayOrPay\Security\Event\EventPick;

use Doctrine\ORM\EntityNotFoundException;
use PlayOrPay\Application\Command\Event\EventPick\ChangeStatus\ChangeEventPickStatusCommand;
use PlayOrPay\Infrastructure\Storage\Event\EventPickRepository;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class ChangeEventPickStatusSecurityHandler extends CommonSecurityHandler
{
    /** @var EventPickRepository */
    private $pickRepo;

    public function __construct(ActorFinder $actorFinder, EventPickRepository $pickRepo)
    {
        parent::__construct($actorFinder);
        $this->pickRepo = $pickRepo;
    }

    /**
     * @param ChangeEventPickStatusCommand $command
     *
     * @throws EntityNotFoundException
     * @throws SecuriryException
     */
    public function __invoke(ChangeEventPickStatusCommand $command): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $pick = $this->pickRepo->get($command->getPickUuid());
        $picker = $pick->getPicker();
        $participant = $picker->getParticipant();
        $this->assertActor($participant->getUser());
    }
}

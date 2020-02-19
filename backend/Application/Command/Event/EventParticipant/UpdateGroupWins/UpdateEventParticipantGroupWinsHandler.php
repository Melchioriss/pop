<?php

namespace PlayOrPay\Application\Command\Event\EventParticipant\UpdateGroupWins;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventParticipantRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\User\ActorFinderInterface;

class UpdateEventParticipantGroupWinsHandler implements CommandHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var EventParticipantRepository */
    private $participantRepo;

    /** @var ActorFinderInterface */
    private $actorFinder;

    public function __construct(EventRepository $eventRepo, EventParticipantRepository $participantRepo, ActorFinderInterface $actorFinder)
    {
        $this->eventRepo = $eventRepo;
        $this->participantRepo = $participantRepo;
        $this->actorFinder = $actorFinder;
    }

    /**
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     */
    public function __invoke(UpdateEventParticipantGroupWinsCommand $command)
    {
        $participant = $this->participantRepo->get($command->getParticipantUuid());
        $participant->updateGroupWins($command->getGroupWins());
        $this->eventRepo->save($participant->getEvent());
    }
}

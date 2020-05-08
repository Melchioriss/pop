<?php

namespace PlayOrPay\Application\Command\Event\EventParticipant\UpdateBlaeoGames;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventParticipantRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;

class UpdateEventParticipantBlaeoGamesHandler implements CommandHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var ActorFinder */
    private $actorFinder;

    private $participantRepo;

    public function __construct(EventRepository $eventRepo, EventParticipantRepository $participantRepo, ActorFinder $actorFinder)
    {
        $this->eventRepo = $eventRepo;
        $this->actorFinder = $actorFinder;
        $this->participantRepo = $participantRepo;
    }

    /**
     * @param UpdateEventParticipantBlaeoGamesCommand $command
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     */
    public function __invoke(UpdateEventParticipantBlaeoGamesCommand $command): void
    {
        $participant = $this->participantRepo->get($command->getParticipantUuid());
        $event = $participant->getEvent();

        $event->updateParticipantBlaeoGames($participant->getUuid(), $command->getBlaeoGames());

        $this->eventRepo->save($event);
    }
}

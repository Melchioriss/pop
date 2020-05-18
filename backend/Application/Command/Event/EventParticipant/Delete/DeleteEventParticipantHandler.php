<?php

namespace PlayOrPay\Application\Command\Event\EventParticipant\Delete;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventParticipantRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;

class DeleteEventParticipantHandler implements CommandHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var EventParticipantRepository */
    private $participantRepo;

    public function __construct(EventRepository $eventRepo, EventParticipantRepository $participantRepo)
    {
        $this->eventRepo = $eventRepo;
        $this->participantRepo = $participantRepo;
    }

    /**
     * @param DeleteEventParticipantCommand $command
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     */
    public function __invoke(DeleteEventParticipantCommand $command): void
    {
        $participant = $this->participantRepo->get($command->participantUuid);
        $event = $participant->getEvent();

        $this->participantRepo->remove($participant);

        $this->eventRepo->save($event);
    }
}

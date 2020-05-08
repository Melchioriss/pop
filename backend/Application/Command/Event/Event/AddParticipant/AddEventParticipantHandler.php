<?php

namespace PlayOrPay\Application\Command\Event\Event\AddParticipant;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

class AddEventParticipantHandler implements CommandHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var UserRepository */
    private $userRepo;

    public function __construct(EventRepository $eventRepo, UserRepository $userRepo)
    {
        $this->eventRepo = $eventRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * @param AddEventParticipantCommand $command
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function __invoke(AddEventParticipantCommand $command): void
    {
        $event = $this->eventRepo->get($command->eventUuid);
        $user = $this->userRepo->get($command->steamId);
        $event->addParticipant($user, $command->participantUuid);
        $this->eventRepo->save($event);
    }
}

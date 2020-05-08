<?php

namespace PlayOrPay\Application\Command\Event\Event\Delete;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;

class DeleteEventHandler implements CommandHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    public function __construct(EventRepository $eventRepo)
    {
        $this->eventRepo = $eventRepo;
    }

    /**
     * @param DeleteEventCommand $command
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function __invoke(DeleteEventCommand $command): void
    {
        $this->eventRepo->remove($this->eventRepo->get($command->eventUuid));
    }
}

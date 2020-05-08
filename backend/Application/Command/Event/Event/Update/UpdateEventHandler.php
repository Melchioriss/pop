<?php

namespace PlayOrPay\Application\Command\Event\Event\Update;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use League\Period\Exception;
use League\Period\Period;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;

class UpdateEventHandler implements CommandHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    public function __construct(EventRepository $eventRepo)
    {
        $this->eventRepo = $eventRepo;
    }

    /**
     * @param UpdateEventCommand $command
     *
     * @throws EntityNotFoundException
     * @throws Exception
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     */
    public function __invoke(UpdateEventCommand $command): void
    {
        $event = $this->eventRepo->get($command->getUuid());
        $event
            ->updateName($command->getName())
            ->updateDescription($command->getDescription())
            ->updateActivePeriod(new Period($command->getStartingOn(), $command->getEndingOn()));

        $this->eventRepo->save($event);
    }
}

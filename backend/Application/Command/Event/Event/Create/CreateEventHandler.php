<?php

namespace PlayOrPay\Application\Command\Event\Event\Create;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use League\Period\Period;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Event\Event;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\Steam\GroupRepository;

class CreateEventHandler implements CommandHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var GroupRepository */
    private $groupRepo;

    public function __construct(EventRepository $eventRepo, GroupRepository $groupRepo)
    {
        $this->eventRepo = $eventRepo;
        $this->groupRepo = $groupRepo;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function __invoke(CreateEventCommand $command)
    {
        $event = new Event(
            $command->getUuid(),
            $command->getName(),
            new Period($command->getStartingOn(), $command->getEndingOn()),
            $command->getDescription(),
            $this->groupRepo->get($command->getGroup())
        );

        $this->eventRepo->save($event);
    }
}

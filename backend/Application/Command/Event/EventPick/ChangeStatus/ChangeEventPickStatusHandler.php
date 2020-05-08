<?php

namespace PlayOrPay\Application\Command\Event\EventPick\ChangeStatus;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventPickRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;

class ChangeEventPickStatusHandler implements CommandHandlerInterface
{
    /** @var EventPickRepository */
    private $pickRepo;

    /** @var EventRepository */
    private $eventRepo;

    public function __construct(EventPickRepository $pickRepo, EventRepository $eventRepo)
    {
        $this->pickRepo = $pickRepo;
        $this->eventRepo = $eventRepo;
    }

    /**
     * @param ChangeEventPickStatusCommand $command
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     * @throws NotFoundException
     */
    public function __invoke(ChangeEventPickStatusCommand $command): void
    {
        $pickUuid = $command->getPickUuid();
        $pick = $this->pickRepo->get($pickUuid);
        $event = $pick->getEvent();

        $event->changePickPlayedStatus($pickUuid, $command->getStatus());

        $this->eventRepo->save($event);
    }
}

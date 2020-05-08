<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\UpdateUser;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventPickerRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

class UpdateEventPickerUserHandler implements CommandHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var EventPickerRepository */
    private $eventPickerRepo;

    /** @var UserRepository */
    private $userRepo;

    public function __construct(EventRepository $eventRepo, EventPickerRepository $eventPickerRepo, UserRepository $userRepo)
    {
        $this->eventRepo = $eventRepo;
        $this->eventPickerRepo = $eventPickerRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * @param UpdateEventPickerUserCommand $command
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     * @throws NotFoundException
     */
    public function __invoke(UpdateEventPickerUserCommand $command): void
    {
        $newPickerUser = $this->userRepo->get($command->userId);
        $picker = $this->eventPickerRepo->get($command->pickerUuid);
        $event = $picker->getParticipant()->getEvent();

        $event->replacePickerUser($command->pickerUuid, $newPickerUser);

        $this->eventRepo->save($event);
    }
}

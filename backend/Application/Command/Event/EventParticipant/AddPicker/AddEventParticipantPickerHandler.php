<?php

namespace PlayOrPay\Application\Command\Event\EventParticipant\AddPicker;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Insideone\Package\EnumFramework\AmbiguousValueException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventParticipantRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;
use ReflectionException;

class AddEventParticipantPickerHandler implements CommandHandlerInterface
{
    /** @var EventParticipantRepository */
    private $participantRepo;

    /** @var UserRepository */
    private $userRepo;

    /** @var EventRepository */
    private $eventRepo;

    public function __construct(EventParticipantRepository $participantRepo, UserRepository $userRepo, EventRepository $eventRepo)
    {
        $this->participantRepo = $participantRepo;
        $this->userRepo = $userRepo;
        $this->eventRepo = $eventRepo;
    }

    /**
     * @param AddEventParticipantPickerCommand $command
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     * @throws AmbiguousValueException
     * @throws ReflectionException
     */
    public function __invoke(AddEventParticipantPickerCommand $command): void
    {
        $participantUuid = $command->getParticipantUuid();

        $participant = $this->participantRepo->get($participantUuid);
        $user = $this->userRepo->get($command->getUserId());
        $event = $participant->getEvent();

        $event->addPicker($command->getPickerUuid(), $participantUuid, $user, $command->getPickerType());
        $this->eventRepo->save($event);
    }
}

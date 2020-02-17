<?php

namespace PlayOrPay\Application\Command\Event\EventParticipant\AddPicker;


use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Event\EventPicker;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventParticipantRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

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
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     */
    public function __invoke(AddEventParticipantPickerCommand $command)
    {
        $participant = $this->participantRepo->get($command->getParticipantUuid());
        $user = $this->userRepo->get($command->getUserId());

        $picker = new EventPicker(
            $command->getPickerUuid(),
            $participant,
            $user,
            $command->getPickerType()
        );

        $participant->addPickers([$picker]);
        $this->eventRepo->save($participant->getEvent());
    }
}
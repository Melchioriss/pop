<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\AddComment;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventPickerCommentRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventPickerRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventPickRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;

class AddEventPickerCommentHandler implements CommandHandlerInterface
{
    /** @var EventPickerRepository */
    private $pickerRepo;

    /** @var EventRepository */
    private $eventRepo;

    /** @var EventPickRepository */
    private $pickRepo;

    /** @var EventPickerCommentRepository */
    private $commentRepo;

    /** @var ActorFinder */
    private $actorFinder;

    public function __construct(
        EventPickerRepository $pickerRepo,
        EventRepository $eventRepo,
        EventPickerCommentRepository $commentRepo,
        EventPickRepository $pickRepo,
        ActorFinder $actorFinder
    ) {
        $this->pickerRepo = $pickerRepo;
        $this->eventRepo = $eventRepo;
        $this->commentRepo = $commentRepo;
        $this->pickRepo = $pickRepo;
        $this->actorFinder = $actorFinder;
    }

    /**
     * @param AddEventPickerCommentCommand $command
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NotFoundException
     * @throws UnallowedOperationException
     * @throws Exception
     */
    public function __invoke(AddEventPickerCommentCommand $command): void
    {
        $actor = $this->actorFinder->getActor();
        $picker = $this->pickerRepo->get($command->pickerUuid);
        $event = $picker->getEvent();

        $event->addPickerComment(
            $command->commentUuid,
            $picker->getUuid(),
            $actor,
            $command->text,
            $command->referencedPickUuid,
            $command->gameReferenceType
        );

        $this->eventRepo->save($event);
    }
}

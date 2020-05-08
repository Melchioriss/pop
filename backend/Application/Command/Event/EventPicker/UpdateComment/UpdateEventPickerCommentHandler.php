<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\UpdateComment;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventPickerCommentRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;

class UpdateEventPickerCommentHandler implements CommandHandlerInterface
{
    /** @var EventPickerCommentRepository */
    private $commentRepo;

    /** @var EventRepository */
    private $eventRepo;

    public function __construct(EventPickerCommentRepository $commentRepo, EventRepository $eventRepo)
    {
        $this->commentRepo = $commentRepo;
        $this->eventRepo = $eventRepo;
    }

    /**
     * @param UpdateEventPickerCommentCommand $command
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     */
    public function __invoke(UpdateEventPickerCommentCommand $command): void
    {
        $comment = $this->commentRepo->get($command->commentUuid);
        $event = $comment->getEvent();

        $comment->updateText($command->text);

        $this->eventRepo->save($event);
    }
}

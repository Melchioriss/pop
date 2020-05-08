<?php

namespace PlayOrPay\Security\Event\EventPicker;

use Doctrine\ORM\EntityNotFoundException;
use PlayOrPay\Application\Command\Event\EventPicker\UpdateComment\UpdateEventPickerCommentCommand;
use PlayOrPay\Infrastructure\Storage\Event\EventPickerCommentRepository;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class UpdateEventPickerCommentSecurityHandler extends CommonSecurityHandler
{
    /** @var EventPickerCommentRepository */
    private $commentRepo;

    public function __construct(ActorFinder $actorFinder, EventPickerCommentRepository $commentRepo)
    {
        parent::__construct($actorFinder);
        $this->commentRepo = $commentRepo;
    }

    /**
     * @param UpdateEventPickerCommentCommand $command
     *
     * @throws EntityNotFoundException
     * @throws SecuriryException
     */
    public function __invoke(UpdateEventPickerCommentCommand $command): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $comment = $this->commentRepo->get($command->commentUuid);
        $this->assertActor($comment->getUser());
    }
}

<?php

namespace PlayOrPay\Security\Event\EventPicker;

use Doctrine\ORM\EntityNotFoundException;
use PlayOrPay\Application\Command\Event\EventPicker\AddComment\AddEventPickerCommentCommand;
use PlayOrPay\Infrastructure\Storage\Event\EventPickerRepository;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class AddEventPickerCommentSecurityHandler extends CommonSecurityHandler
{
    /** @var EventPickerRepository */
    private $pickerRepo;

    public function __construct(ActorFinder $actorFinder, EventPickerRepository $pickerRepo)
    {
        parent::__construct($actorFinder);
        $this->pickerRepo = $pickerRepo;
    }

    /**
     * @param AddEventPickerCommentCommand $command
     *
     * @throws EntityNotFoundException
     * @throws SecuriryException
     */
    public function __invoke(AddEventPickerCommentCommand $command): void
    {
        $picker = $this->pickerRepo->get($command->pickerUuid);
        $this->assertBeingOneOf([$picker->getUser(), $picker->getParticipant()->getUser()], true);
    }
}

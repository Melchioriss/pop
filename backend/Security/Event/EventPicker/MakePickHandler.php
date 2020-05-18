<?php

namespace PlayOrPay\Security\Event\EventPicker;

use Doctrine\ORM\EntityNotFoundException;
use PlayOrPay\Application\Command\Event\EventPicker\MakePick\MakePickCommand;
use PlayOrPay\Infrastructure\Storage\Event\EventPickerRepository;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class MakePickHandler extends CommonSecurityHandler
{
    /** @var EventPickerRepository */
    private $pickerRepo;

    public function __construct(ActorFinder $actorFinder, EventPickerRepository $pickerRepo)
    {
        parent::__construct($actorFinder);
        $this->pickerRepo = $pickerRepo;
    }

    /**
     * @param MakePickCommand $command
     *
     * @throws EntityNotFoundException
     * @throws SecuriryException
     */
    public function __invoke(MakePickCommand $command): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $picker = $this->pickerRepo->get($command->pickerUuid);
        $this->assertActor($picker->getUser());
    }
}

<?php

namespace PlayOrPay\Security\Event\EventPicker;

use Doctrine\ORM\EntityNotFoundException;
use PlayOrPay\Application\Command\Event\EventPicker\ChangePickGame\ChangePickGameCommand;
use PlayOrPay\Infrastructure\Storage\Event\EventPickerRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventPickRepository;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class ChangePickSecurityHandler extends CommonSecurityHandler
{
    /** @var EventPickerRepository */
    private $pickerRepo;

    /** @var EventPickRepository */
    private $pickRepo;

    public function __construct(ActorFinder $actorFinder, EventPickerRepository $pickerRepo, EventPickRepository $pickRepo)
    {
        parent::__construct($actorFinder);
        $this->pickerRepo = $pickerRepo;
        $this->pickRepo = $pickRepo;
    }

    /**
     * @param ChangePickGameCommand $command
     *
     * @throws EntityNotFoundException
     * @throws SecuriryException
     */
    public function __invoke(ChangePickGameCommand $command): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $pick = $this->pickRepo->get($command->pickUuid);
        $this->assertActor($pick->getPicker()->getUser());
    }
}

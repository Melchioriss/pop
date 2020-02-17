<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\ChangePick;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventPickerRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\Steam\GameRepository;

class ChangePickHandler implements CommandHandlerInterface
{
    /** @var EventPickerRepository */
    private $pickerRepo;

    /** @var GameRepository */
    private $gameRepo;

    /** @var EventRepository */
    private $eventRepo;

    public function __construct(EventPickerRepository $pickerRepo, GameRepository $gameRepo, EventRepository $eventRepo)
    {
        $this->pickerRepo = $pickerRepo;
        $this->gameRepo = $gameRepo;
        $this->eventRepo = $eventRepo;
    }

    /**
     * @param ChangePickCommand $command
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NotFoundException
     * @throws UnallowedOperationException
     */
    public function __invoke(ChangePickCommand $command)
    {
        $game = $this->gameRepo->get($command->gameId);

        $picker = $this->pickerRepo->get($command->pickerUuid);
        $pick = $picker->getPick($command->pickUuid);

        $pick->changeGame($game);

        $this->eventRepo->save($picker->getParticipant()->getEvent());
    }
}

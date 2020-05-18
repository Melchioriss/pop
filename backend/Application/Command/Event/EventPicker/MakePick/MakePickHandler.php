<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\MakePick;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventPickerRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\Game\GameRepository;

class MakePickHandler implements CommandHandlerInterface
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
     * @param MakePickCommand $command
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     * @throws NotFoundException
     */
    public function __invoke(MakePickCommand $command): void
    {
        $game = $this->gameRepo->get((string) $command->gameId);
        $picker = $this->pickerRepo->get($command->pickerUuid);
        $event = $picker->getEvent();

        $event->makePick($command->pickerUuid, $command->pickUuid, $command->type, $game);

        $this->eventRepo->save($event);
    }
}

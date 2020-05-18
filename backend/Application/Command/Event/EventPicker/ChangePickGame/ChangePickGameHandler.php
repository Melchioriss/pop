<?php

namespace PlayOrPay\Application\Command\Event\EventPicker\ChangePickGame;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventPickRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\Game\GameRepository;

class ChangePickGameHandler implements CommandHandlerInterface
{
    /** @var EventPickRepository */
    private $pickRepo;

    /** @var GameRepository */
    private $gameRepo;

    /** @var EventRepository */
    private $eventRepo;

    public function __construct(GameRepository $gameRepo, EventRepository $eventRepo, EventPickRepository $pickRepo)
    {
        $this->pickRepo = $pickRepo;
        $this->gameRepo = $gameRepo;
        $this->eventRepo = $eventRepo;
    }

    /**
     * @param ChangePickGameCommand $command
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NotFoundException
     * @throws UnallowedOperationException
     */
    public function __invoke(ChangePickGameCommand $command): void
    {
        $game = $this->gameRepo->get((string) $command->gameId);
        $pick = $this->pickRepo->get($command->pickUuid);
        $event = $pick->getEvent();

        $event->changePickGame($command->pickUuid, $game);

        $this->eventRepo->save($event);
    }
}

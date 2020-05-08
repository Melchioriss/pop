<?php

namespace PlayOrPay\Application\Command\Event\EventParticipant\UpdateBlaeoPoints;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Event\RewardReason;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventParticipantRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRewardRepository;

class UpdateEventParticipantBlaeoPointsHandler implements CommandHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var EventParticipantRepository */
    private $participantRepo;

    /** @var EventRewardRepository */
    private $rewardRepo;

    public function __construct(
        EventRepository $eventRepo,
        EventParticipantRepository $participantRepo,
        EventRewardRepository $rewardRepo
    ) {
        $this->eventRepo = $eventRepo;
        $this->participantRepo = $participantRepo;
        $this->rewardRepo = $rewardRepo;
    }

    /**
     * @param UpdateEventParticipantBlaeoPointsCommand $command
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     * @throws Exception
     */
    public function __invoke(UpdateEventParticipantBlaeoPointsCommand $command): void
    {
        $blaeoGamesAchievement = $this->rewardRepo->get(RewardReason::BLAEO_GAMES);

        $participant = $this->participantRepo->get($command->participantUuid);
        $event = $participant->getEvent();

        $event->updateParticipantBlaeoPoints($command->participantUuid, $blaeoGamesAchievement, $command->blaeoPoints);

        $this->eventRepo->save($event);
    }
}

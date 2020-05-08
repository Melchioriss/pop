<?php

namespace PlayOrPay\Domain\Event\DomainEvent\Handler;

use Exception;
use PlayOrPay\Domain\Contracts\DomainEvent\DomainEventHandlerInterface;
use PlayOrPay\Domain\Event\DomainEvent\Event\PickPlayedStatusChanged;
use PlayOrPay\Domain\Event\RewardReason;
use PlayOrPay\Domain\Event\SuitablePickRewardFinder;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Infrastructure\Storage\Event\EventRewardRepository;

class UpdateRewardsOnPickPlayedStatusChanged implements DomainEventHandlerInterface
{
    /** @var SuitablePickRewardFinder */
    private $findSuitableRewards;

    /** @var EventRewardRepository */
    private $rewardRepo;

    public function __construct(SuitablePickRewardFinder $findSuitableRewards, EventRewardRepository $rewardRepo)
    {
        $this->findSuitableRewards = $findSuitableRewards;
        $this->rewardRepo = $rewardRepo;
    }

    /**
     * @param PickPlayedStatusChanged $fact
     *
     * @throws NotFoundException
     * @throws Exception
     */
    public function __invoke(PickPlayedStatusChanged $fact): void
    {
        $pickUuid = $fact->pick->getUuid();
        $event = $fact->pick->getEvent();
        $pickType = $fact->pick->getType();
        $participant = $fact->pick->getParticipant();

        $newRewards = ($this->findSuitableRewards)($fact->to, $pickType);

        $event->setupPickRewards($participant->getUuid(), $newRewards, $pickUuid);

        $allBeatenReward = $this->rewardRepo->get(RewardReason::ALL_PICKS_BEATEN);
        if ($participant->hasBeatenAllPicks()) {
            $participant->setupReward($allBeatenReward, null);
        } elseif ($participant->hasReward($allBeatenReward, null)) {
            $participant->removeReward($allBeatenReward->getReason(), null);
        }
    }
}

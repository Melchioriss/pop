<?php

namespace PlayOrPay\Tests\Functional\EventEarnedReward;

use Doctrine\ORM\EntityNotFoundException;
use Exception;
use PlayOrPay\Domain\Event\Event;
use PlayOrPay\Domain\Event\EventParticipant;
use PlayOrPay\Domain\Event\EventPick;
use PlayOrPay\Domain\Event\EventPickPlayedStatus;
use PlayOrPay\Domain\Event\RewardReason;
use PlayOrPay\Domain\Event\SuitablePickRewardFinder;
use PlayOrPay\Infrastructure\Storage\Event\EventParticipantRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRewardRepository;
use PlayOrPay\Tests\Functional\FunctionalTest;

class EventEarnedRewardTest extends FunctionalTest
{
    /**
     * @test
     *
     * @throws Exception
     */
    public function setting_up_blaeo_games_points_should_be_rewarded_and_unsetting_should_remove_reward(): void
    {
        $fixtures = $this->applyFixtures(__DIR__ . '/../../fixtures/filled_event.yaml');

        /** @var Event $event */
        $event = $fixtures->get('filled_event');

        $participant = $event->getParticipants()[0];

        $blaeoGamesReason = new RewardReason(RewardReason::BLAEO_GAMES);

        $rewardedPoints = 10;

        $this->authorizeAsAdmin();

        $this->request('update_event_participant_blaeo_points', [
            'participantUuid' => $participant->getUuid()->toString(),
            'blaeoPoints'     => $rewardedPoints,
        ]);

        /** @var EventRepository $eventRepo */
        $eventRepo = self::$container->get(EventRepository::class);
        $event = $eventRepo->get($event->getUuid());

        $reward = $event->fetchReward($participant->getUuid(), $blaeoGamesReason, null);
        $this->assertSame($rewardedPoints, $reward->getValue(), "Reward wasn't created as expected");

        $this->request('update_event_participant_blaeo_points', [
            'participantUuid' => $participant->getUuid()->toString(),
            'blaeoPoints'     => 0,
        ]);

        $event = $eventRepo->get($event->getUuid());

        $reward = $event->fetchReward($participant->getUuid(), $blaeoGamesReason, null);
        $this->assertNull($reward, "Reward wasn't removed as expected");
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function marking_game_as_completed_should_give_two_rewards_and_remarking_should_remove_them(): void
    {
        $pick = $this->prepareCompleted();
        $participant = $pick->getParticipant();

        $participant = $this->refreshParticipant($participant);
        $this->assertCount(2, $participant->getRewards(), 'There were expected some rewards');

        $this->request('change_pick_status', [
            'pickUuid' => $pick->getUuid()->toString(),
            'status'   => EventPickPlayedStatus::ABANDONED,
        ]);

        $participant = $this->refreshParticipant($participant);
        $this->assertCount(0, $participant->getRewards(), 'Unexpected rewards have been found');
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function marking_completed_game_as_beaten_should_remove_completed_reward_but_keep_beaten_reward(): void
    {
        $pick = $this->prepareCompleted();
        $participant = $pick->getParticipant();

        $beatenStatus = new EventPickPlayedStatus(EventPickPlayedStatus::BEATEN);

        $this->request('change_pick_status', [
            'pickUuid' => $pick->getUuid()->toString(),
            'status'   => $beatenStatus->__default,
        ]);

        $participant = $this->refreshParticipant($participant);
        $rewards = $participant->getRewards();
        $this->assertCount(1, $rewards, 'There must be only one reward');

        /** @var SuitablePickRewardFinder $suitablePickRewardFinder */
        $suitablePickRewardFinder = self::$container->get(SuitablePickRewardFinder::class);
        $suitableRewards = $suitablePickRewardFinder($beatenStatus, $pick->getType());
        $this->assertCount(1, $suitableRewards);

        $this->assertSame(
            reset($suitableRewards)->getReason()->getCodename(),
            reset($rewards)->getReason()->getCodename(),
            'Unexpected reward was kept'
        );
    }

    /**
     * @throws Exception
     *
     * @return EventPick
     */
    private function prepareCompleted(): EventPick
    {
        $fixtures = $this->applyFixtures(__DIR__ . '/../../fixtures/filled_event.yaml');

        /** @var Event $event */
        $event = $fixtures->get('filled_event');

        $participant = $event->getParticipants()[0];
        $this->assertEmpty($participant->getRewards());

        $pick = $participant->getPicks()[0];

        $this->authorizeAsAdmin();
        $this->request('change_pick_status', [
            'pickUuid' => $pick->getUuid()->toString(),
            'status'   => EventPickPlayedStatus::COMPLETED,
        ]);

        return $pick;
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function marking_all_completed_should_be_properly_rewarded_and_unmarking_just_one_should_remove_that_reward(): void
    {
        $fixtures = $this->applyFixtures(__DIR__ . '/../../fixtures/filled_event.yaml');
        /** @var Event $event */
        $event = $fixtures->get('filled_event');

        $this->authorizeAsAdmin();

        /** @var EventRewardRepository $rewardRepo */
        $rewardRepo = self::$container->get(EventRewardRepository::class);
        $allBeatenReward = $rewardRepo->get(RewardReason::ALL_PICKS_BEATEN);

        $participant = $event->getParticipants()[0];
        $this->assertEmpty($participant->getRewards());

        // mark all as beaten except first
        $picks = $participant->getPicks();
        foreach ($picks as $idx => $pick) {
            if ($idx === 0) {
                continue;
            }

            $this->request('change_pick_status', [
                'pickUuid' => $pick->getUuid()->toString(),
                'status'   => EventPickPlayedStatus::BEATEN,
            ]);
        }

        $participant = $this->refreshParticipant($participant);
        $this->assertFalse($participant->hasReward($allBeatenReward, null), "There mustn't be all-beaten reward");

        $this->request('change_pick_status', [
            'pickUuid' => $picks[0]->getUuid()->toString(),
            'status'   => EventPickPlayedStatus::BEATEN,
        ]);

        $participant = $this->refreshParticipant($participant);
        $this->assertTrue($participant->hasReward($allBeatenReward, null), 'There must be all-beaten reward');

        $this->request('change_pick_status', [
            'pickUuid' => $picks[0]->getUuid()->toString(),
            'status'   => EventPickPlayedStatus::NOT_PLAYED,
        ]);

        $participant = $this->refreshParticipant($participant);
        $this->assertFalse($participant->hasReward($allBeatenReward, null), "There mustn't be all-beaten reward");
    }

    /**
     * @param EventParticipant $participant
     *
     * @throws EntityNotFoundException
     *
     * @return EventParticipant
     */
    private function refreshParticipant(EventParticipant $participant): EventParticipant
    {
        /** @var EventParticipantRepository $participantRepo */
        $participantRepo = self::$container->get(EventParticipantRepository::class);

        return $participantRepo->get($participant->getUuid());
    }
}

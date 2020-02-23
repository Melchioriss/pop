<?php

namespace PlayOrPay\Tests\Functional\EventEarnedReward;

use Exception;
use PlayOrPay\Domain\Event\Event;
use PlayOrPay\Domain\Event\RewardReason;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Tests\Functional\FunctionalTest;

class EventEarnedRewardTest extends FunctionalTest
{
    /**
     * @test
     * @throws Exception
     */
    public function setting_up_blaeo_games_points_should_be_rewarded_and_unsetting_should_remove_reward()
    {
        $fixtures = $this->applyFixtures(__DIR__.'/../../fixtures/filled_event.yaml');

        /** @var Event $event */
        $event = $fixtures->get('filled_event');

        $participant = $event->getParticipants()[0];

        $blaeoGamesReason = new RewardReason(RewardReason::BLAEO_GAMES);

        $rewardedPoints = 10;

        $this->authorizeAsAdmin();

        $this->request('update_event_participant_blaeo_points', [
            'participantUuid' => (string) $participant->getUuid(),
            'blaeoPoints' => $rewardedPoints,
        ]);

        /** @var EventRepository $eventRepo */
        $eventRepo = self::$container->get(EventRepository::class);
        $event = $eventRepo->get($event->getUuid());

        $reward = $event->fetchReward($participant->getUuid(), $blaeoGamesReason, null);
        $this->assertNotNull($reward, "Reward wasn't had been created as expected");

        $this->request('update_event_participant_blaeo_points', [
            'participantUuid' => (string) $participant->getUuid(),
            'blaeoPoints' => 0,
        ]);

        $this->assertSuccessfulResponse();

        $event = $eventRepo->get($event->getUuid());

        $reward = $event->fetchReward($participant->getUuid(), $blaeoGamesReason, null);
        $this->assertNull($reward, "Reward wasn't removed as expected");
    }
}

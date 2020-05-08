<?php

namespace PlayOrPay\Tests\Functional\EventPick;

use Doctrine\ORM\EntityNotFoundException;
use Exception;
use PlayOrPay\Domain\Event\Event;
use PlayOrPay\Domain\Event\EventCommentGameReferenceType;
use PlayOrPay\Domain\Event\EventPick;
use PlayOrPay\Domain\Event\EventPickPlayedStatus;
use PlayOrPay\Domain\Event\EventPickType;
use PlayOrPay\Domain\Game\Game;
use PlayOrPay\Infrastructure\Storage\Event\EventPickRepository;
use PlayOrPay\Tests\Functional\FunctionalTest;
use Ramsey\Uuid\Uuid;

class EventPickTest extends FunctionalTest
{
    /** @var EventPickRepository|null */
    private $pickRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pickRepo = self::$container->get(EventPickRepository::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->pickRepo = null;
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function reject_rejecting_beaten_game(): void
    {
        $fixtures = $this->applyFixtures(__DIR__ . '/../../fixtures/filled_event.yaml');

        /** @var Event $event */
        $event = $fixtures->get('filled_event');

        $pick = $event->getPickers()[0]->getPicks()[0];

        $this->assertFalse($pick->isRejected(), 'Pick should not be rejected before rejection');

        $this->authorizeAsAdmin();

        $this->request('change_pick_status', [
            'pickUuid' => $pick->getUuid()->toString(),
            'status'   => EventPickPlayedStatus::BEATEN,
        ]);

        $this->request('add_comment', [
            'commentUuid'        => Uuid::uuid4()->toString(),
            'pickerUuid'         => $pick->getPicker()->getUuid()->toString(),
            'text'               => "I don't want this game",
            'referencedPickUuid' => $pick->getUuid()->toString(),
            'gameReferenceType'  => EventCommentGameReferenceType::REPICK,
        ], false);

        $this->assertResponseHavingString('Played status must be one of these');
    }

    /**
     * @test
     *
     * @throws EntityNotFoundException
     * @throws Exception
     */
    public function pick_should_be_successfully_rejected_and_activated_after_changing_game(): void
    {
        $fixtures = $this->applyFixtures(__DIR__ . '/../../fixtures/filled_event.yaml');

        /** @var Event $event */
        $event = $fixtures->get('filled_event');

        $pick = $event->getPickers()[0]->getPicks()[0];

        $this->assertFalse($pick->isRejected(), 'Pick should not be rejected before rejection');

        $this->authorizeAsAdmin();

        $this->request('add_comment', [
            'commentUuid'        => Uuid::uuid4()->toString(),
            'pickerUuid'         => $pick->getPicker()->getUuid()->toString(),
            'text'               => "I don't want this game",
            'referencedPickUuid' => $pick->getUuid()->toString(),
            'gameReferenceType'  => EventCommentGameReferenceType::REPICK,
        ]);

        $this->refreshPick($pick);

        $this->assertTrue($pick->isRejected(), 'Pick should be in rejected state');

        /** @var Game $newGame */
        $newGame = $fixtures->findAllOf(Game::class, null, [$pick->getGame()])[0];
        $this->request('change_pick_game', [
            'pickUuid' => $pick->getUuid()->toString(),
            'gameId'   => (string) $newGame->getId(),
        ]);

        $this->refreshPick($pick);

        $this->assertTrue($pick->isActive(), 'Pick should be active after changing game');
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function changing_pick_game_to_duplicated_game_should_be_rejected(): void
    {
        $fixtures = $this->applyFixtures(__DIR__ . '/../../fixtures/filled_event.yaml');

        /** @var Event $event */
        $event = $fixtures->get('filled_event');

        $participant = $event->getParticipants()[0];
        [$firstPick, $secondPick] = $participant->getPicks();

        $this->assertFalse(
            $firstPick->getGame()->getId()->equalTo($secondPick->getGame()->getId()),
            'First and second picks should have different games to continue'
        );

        $this->authorizeAsAdmin();
        $this->request('change_pick_game', [
            'pickUuid' => $firstPick->getUuid()->toString(),
            'gameId'   => (string) $secondPick->getGame()->getId(),
        ], false);

        $this->assertNonSuccessfulResponse();

        $this->refreshPick($firstPick);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function making_two_picks_for_the_same_game_for_a_participant_should_be_rejected(): void
    {
        $fixtures = $this->applyFixtures(__DIR__ . '/../../fixtures/empty_event.yaml');

        /** @var Event $event */
        $event = $fixtures->get('empty_event');

        $event->generatePickers();
        $event->generatePicks([$fixtures->findAllOf(Game::class)[0]]);

        $this->save();

        $pick = $event->getPicks()[0];

        $picker = $pick->getPicker();

        $this->authorizeAsAdmin();
        $this->request('make_pick', [
            'pickUuid'   => $pick->getUuid()->toString(),
            'pickerUuid' => $picker->getUuid()->toString(),
            'type'       => EventPickType::VERY_LONG,
            'gameId'     => (string) $pick->getGame()->getId(),
        ], false);

        $this->assertResponseHavingString('Participant already has a pick for game');
    }

    /**
     * @param EventPick $pick
     *
     * @throws EntityNotFoundException
     */
    private function refreshPick(EventPick &$pick): void
    {
        $pick = $this->pickRepo->get($pick->getUuid());
    }
}

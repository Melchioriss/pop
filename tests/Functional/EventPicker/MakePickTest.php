<?php

namespace PlayOrPay\Tests\Functional\EventPicker;

use Exception;
use PlayOrPay\Domain\Event\Event;
use PlayOrPay\Domain\Event\EventPicker;
use PlayOrPay\Domain\Event\EventPickType;
use PlayOrPay\Domain\Game\Game;
use PlayOrPay\Tests\Functional\FunctionalTest;
use Ramsey\Uuid\Uuid;

class MakePickTest extends FunctionalTest
{
    /**
     * @test
     *
     * @throws Exception
     */
    public function make_new_pick_shoud_be_successful(): void
    {
        $fixtures = $this->applyFixtures(__DIR__ . '/../../fixtures/empty_event.yaml');

        /** @var Event $event */
        $event = $fixtures->get('empty_event');
        $event->generatePickers();
        $this->save();

        /** @var EventPicker[] $pickers */
        $pickers = $event->getPickers();
        $this->assertNotEmpty($pickers);

        $picker = $pickers[0];

        /** @var Game $game */
        $game = $fixtures->getOneOf(Game::class);
        $pickType = new EventPickType(EventPickType::MEDIUM);

        $this->authorizeAsAdmin();
        $this->request('make_pick', [
            'pickUuid'   => Uuid::uuid4()->toString(),
            'pickerUuid' => $picker->getUuid()->toString(),
            'type'       => (string) $pickType,
            'gameId'     => (string) $game->getId(),
        ]);

        $this->assertSame($game, $picker->getPickOfType($pickType)->getGame());
    }

    /**
     * @todo @test
     */
    public function make_a_pick_for_a_type_that_already_picked_should_fail(): void
    {
    }
}

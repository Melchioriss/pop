<?php

namespace PlayOrPay\Tests\Functional\Event;

use Exception;
use PlayOrPay\Domain\Event\Event;
use PlayOrPay\Domain\User\User;
use PlayOrPay\Tests\Functional\FunctionalTest;

class UpdateEventPickerUserTest extends FunctionalTest
{
    /**
     * @test
     *
     * @throws Exception
     */
    public function should_update_successfully(): void
    {
        $fixtures = $this->applyFixtures(__DIR__ . '/../../fixtures/empty_event.yaml');

        /** @var Event $event */
        $event = $fixtures->get('empty_event');
        $event->generatePickers();
        $this->save();

        $pickers = $event->getPickers();
        $this->assertNotCount(0, $pickers);

        $updatedPicker = $pickers[0];
        $oldUser = $updatedPicker->getUser();

        /** @var User $newUser */
        $newUser = $fixtures->getOneOf(User::class, [$oldUser]);

        $this->authorizeAsAdmin();
        $this->request('replace_event_picker', [
            'pickerUuid' => $updatedPicker->getUuid()->toString(),
            'userId'     => (string) $newUser->getSteamId(),
        ]);

        $this->assertSame($newUser, $updatedPicker->getUser());
    }
}

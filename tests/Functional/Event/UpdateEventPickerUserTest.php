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
     * @throws Exception
     */
    public function should_update_successfuly()
    {
        $fixtures = $this->applyFixtures(__DIR__.'/../../fixtures/default.yaml');

        /** @var Event $event */
        $event = $fixtures->getOneOf(Event::class);
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
            'pickerUuid' => (string)$updatedPicker->getUuid(),
            'userId' => (string)$newUser->getSteamId(),
        ]);

        $this->assertSuccessfulResponse();
        $this->assertSame($newUser, $updatedPicker->getUser());
    }
}

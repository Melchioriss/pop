<?php

namespace PlayOrPay\Tests\Functional\Event;

use Doctrine\DBAL\DBALException;
use DomainException;
use Exception;
use PlayOrPay\Domain\Event\Event;
use PlayOrPay\Domain\User\User;
use PlayOrPay\Infrastructure\Storage\Event\EventParticipantRepository;
use PlayOrPay\Tests\Functional\FixtureCollection;
use PlayOrPay\Tests\Functional\FunctionalTest;

class AddEventParticipantTest extends FunctionalTest
{
    /** @var FixtureCollection|null */
    private $fixtures;

    /** @var Event */
    private $event;

    /** @var EventParticipantRepository */
    private $participantRepo;

    /**
     * @throws DBALException
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->fixtures = $this->applyFixtures(__DIR__ . '/../../fixtures/empty_event.yaml');
        $this->event = $this->fixtures->get('empty_event');
        $this->participantRepo = self::$container->get(EventParticipantRepository::class);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function should_add_new_participant(): void
    {
        /** @var User $admin */
        $admin = $this->fixtures->get('admin');

        $this->event->getGroup()->addMember($admin);
        $this->save();

        $this->authorizeAsAdmin();
        $this->request('add_participant', [
            'participantUuid' => $this->participantRepo->nextUuid()->toString(),
            'eventUuid'       => $this->event->getUuid()->toString(),
            'steamId'         => (int) (string) $admin->getSteamId(),
        ]);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function should_not_add_new_participant_who_is_already_a_participant(): void
    {
        $participant = $this->event->getParticipants()[0];
        $this->expectException(DomainException::class);
        $this->event->addParticipant($participant->getUser());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function should_not_add_new_participant_who_doesnt_belong_to_the_group(): void
    {
        $this->expectException(DomainException::class);
        $admin = $this->fixtures->get('admin');
        if (!$admin instanceof User) {
            throw new Exception('admin fixture should be User');
        }

        $this->event->addParticipant($admin);
    }
}

<?php

namespace PlayOrPay\Domain\Event;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use DomainException;
use Exception;
use League\Period\Period;
use PlayOrPay\Domain\Contracts\Entity\AggregateInterface;
use PlayOrPay\Domain\Contracts\Entity\OnUpdateEventListenerInterface;
use PlayOrPay\Domain\Contracts\Exception\ForbiddenActionException;
use PlayOrPay\Domain\Event\Exception\WrongParticipantException;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Domain\Steam\Group;
use PlayOrPay\Domain\User\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Event implements OnUpdateEventListenerInterface, AggregateInterface
{
    /** @var UuidInterface */
    private $uuid;

    /** @var string */
    private $name;

    /** @var Group */
    private $group;

    /** @var Period */
    private $activePeriod;

    /** @var string */
    private $description;

    /** @var DateTime */
    private $createdAt;

    /** @var DateTime */
    private $updatedAt;

    /** @var EventParticipant[] */
    private $participants;

    /**
     * Event constructor.
     *
     * @param UuidInterface $uuid
     * @param string        $name
     * @param Period        $activePeriod
     * @param string        $description
     * @param Group         $group
     *
     * @throws Exception
     */
    public function __construct(UuidInterface $uuid, string $name, Period $activePeriod, string $description, Group $group)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->activePeriod = $activePeriod;
        $this->description = $description;
        $this->createdAt = $this->updatedAt = new DateTime();
        $this->participants = new ArrayCollection();
        $this->fillParticipants($group);
    }

    /**
     * @param User $user
     *
     * @throws Exception
     *
     * @return EventParticipant
     */
    private function makeParticipant(User $user, UuidInterface $participantUuid = null)
    {
        if (!$this->group->hasUser($user)) {
            throw new DomainException(sprintf("User should be in the group '%s' to be a participant", $this->group->getName()));
        }

        if ($this->hasParticipant($user)) {
            throw new DomainException(sprintf("The user '%s' is already a participant", $user->getProfileName()));
        }

        $uuid = $participantUuid ? $participantUuid : Uuid::uuid4();

        return new EventParticipant($uuid, $this, $user, '', '', (string) $user->getExtraRules());
    }

    /**
     * @throws Exception
     */
    private function fillParticipants(Group $group)
    {
        $this->group = $group;
        $this->participants->clear();

        foreach ($this->group->getActiveMembers() as $member) {
            $this->participants->add($this->makeParticipant($member));
        }
    }

    /**
     * @throws Exception
     */
    public function generatePickers()
    {
        /** @var User[] $minorPickers */
        $minorPickers = [];

        /** @var User[] $majorPickers */
        $majorPickers = [];

        foreach ($this->participants as $participant) {
            $minorPickers[] = $participant->getUser();
            $majorPickers[] = $participant->getUser();
        }

        $maxKey = count($this->participants) - 1;

        $notGenerated = true;

        while ($notGenerated) {
            shuffle($minorPickers);
            shuffle($majorPickers);
            shuffle($majorPickers);

            $notGenerated = false;

            foreach ($this->participants as $key => $participant) {
                $participantSteamId = $participant->getUser()->getSteamId();
                $minorPickerSteamId = $minorPickers[$key]->getSteamId();
                $majorPickerSteamId = $majorPickers[$key]->getSteamId();

                if ($participantSteamId === $minorPickerSteamId) {
                    if ($key != $maxKey) {
                        $currentPicker = $minorPickers[$key];
                        $minorPickers[$key] = $minorPickers[$key + 1];
                        $minorPickers[$key + 1] = $currentPicker;

                        $minorPickerSteamId = $minorPickers[$key]->getSteamId();
                    }
                }

                if (($participantSteamId === $majorPickerSteamId) || ($minorPickerSteamId === $majorPickerSteamId)) {
                    if ($key != $maxKey) {
                        $currentPicker = $majorPickers[$key];
                        $majorPickers[$key] = $majorPickers[$key + 1];
                        $majorPickers[$key + 1] = $currentPicker;

                        $majorPickerSteamId = $majorPickers[$key]->getSteamId();
                    }
                }

                if (
                    ($participantSteamId === $minorPickerSteamId)
                    ||
                    ($participantSteamId === $majorPickerSteamId)
                    ||
                    ($minorPickerSteamId === $majorPickerSteamId)
                ) {
                    // something went wrong, we'll need to try again
                    $notGenerated = true;
                }
            }
        }

        $participants = [];

        foreach ($this->participants as $key => $participant) {
            $participant->getUser();

            $minorPicker = new EventPicker(
                Uuid::uuid4(),
                $participant,
                $minorPickers[$key],
                new EventPickerType(EventPickerType::MINOR)
            );

            $majorPicker = new EventPicker(
                Uuid::uuid4(),
                $participant,
                $majorPickers[$key],
                new EventPickerType(EventPickerType::MAJOR)
            );

            $participant->addPickers([$minorPicker, $majorPicker]);
            $participants[] = $participant;
        }

        $this->participants->clear();

        foreach ($participants as $participant) {
            $this->participants->add($participant);
        }
    }

    /**
     * @throws NotFoundException
     */
    public function getPicker(UuidInterface $needlePicker): EventPicker
    {
        foreach ($this->participants as $participant) {
            foreach ($participant->getPickers() as $picker) {
                if ($picker->getUuid()->equals($needlePicker)) {
                    return $picker;
                }
            }
        }

        throw NotFoundException::forObject(EventPicker::class, (string) $needlePicker);
    }

    /**
     * @throws NotFoundException
     *
     * @return Event
     */
    public function replacePicker(UuidInterface $pickerUuid, User $newUser): self
    {
        $picker = $this->getPicker($pickerUuid);
        $picker->replaceUser($newUser);

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    /** @return EventParticipant[] */
    public function getParticipants(): array
    {
        return $this->participants->toArray();
    }

    /**
     * @param User $user
     *
     * @throws Exception
     *
     * @return Event
     */
    public function addParticipant(User $user, UuidInterface $participantUuid = null): self
    {
        $this->participants->add($this->makeParticipant($user, $participantUuid));

        return $this;
    }

    public function hasParticipant(User $user): bool
    {
        return $this->participants->exists(function (
            /* @noinspection PhpUnusedParameterInspection */
            int $idx,
            EventParticipant $eventParticipant
        ) use ($user) {
            return $eventParticipant->getUser() === $user;
        });
    }

    /** @return User[] */
    public function getUsers(): array
    {
        return $this->participants->map(function (EventParticipant $participant) {
            return $participant->getUser();
        })->toArray();
    }

    public function onUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function updateName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function updateDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function updateActivePeriod(Period $activePeriod): self
    {
        $this->activePeriod = $activePeriod;

        return $this;
    }

    private function findParticipant(UuidInterface $participantUuid): ?EventParticipant
    {
        $foundParticipant = null;

        $this->participants->exists(
            function (
                /* @noinspection PhpUnusedParameterInspection */
                int $idx,
                EventParticipant $participant
            ) use ($participantUuid, &$foundParticipant) {
                if ($participant->getUuid()->equals($participantUuid)) {
                    $foundParticipant = $participant;

                    return true;
                }

                return false;
            }
        );

        return $foundParticipant;
    }

    /**
     * @param UuidInterface $participantUuid
     *
     * @return EventParticipant
     */
    private function getParticipant(UuidInterface $participantUuid): EventParticipant
    {
        $participant = $this->findParticipant($participantUuid);
        if (!$participant) {
            throw WrongParticipantException::becauseTheyDontBelongToEvent($participantUuid, $this->getUuid());
        }

        return $participant;
    }

    public function updateEventParticipantBlaeoWins(?User $actor, UuidInterface $participantUuid, string $blaeoGames): self
    {
        $participant = $this->getParticipant($participantUuid);
        $this->shouldBeAllowedToUpdateEventParticipant($actor, $participant);
        $participant->updateBlaeoGames($blaeoGames);

        return $this;
    }

    public function updateEventParticipantGroupWins(?User $actor, UuidInterface $participantUuid, string $groupWins): self
    {
        $participant = $this->getParticipant($participantUuid);
        $this->shouldBeAllowedToUpdateEventParticipant($actor, $participant);
        $participant->updateGroupWins($groupWins);

        return $this;
    }

    private function shouldBeAllowedToUpdateEventParticipant(?User $actor, EventParticipant $participant)
    {
        if ($actor->isAdmin()) {
            return;
        }

        if ($actor === $participant->getUser()) {
            return;
        }

        throw ForbiddenActionException::forThisActor($actor, __METHOD__);
    }

    /**
     * @return EventPicker[]
     */
    public function getPickers(): array
    {
        $pickers = [];
        foreach ($this->participants as $participant) {
            array_push($pickers, ...$participant->getPickers());
        }

        return $pickers;
    }

    /**
     * @return User[]
     */
    public function getPotentialParticipants(): array
    {
        $userCollection = new ArrayCollection($this->getUsers());

        return array_filter($this->group->getMembers(), function (User $user) use ($userCollection) {
            return !$userCollection->contains($user);
        });
    }

    /**
     * @param UuidInterface $commentUuid
     * @param UuidInterface $pickerUuid
     * @param User          $user
     * @param string        $text
     *
     * @throws NotFoundException
     *
     * @return Event
     */
    public function addPickerComment(UuidInterface $commentUuid, UuidInterface $pickerUuid, User $user, string $text): self
    {
        $this->getPicker($pickerUuid)->addComment($commentUuid, $user, $text);

        return $this;
    }
}

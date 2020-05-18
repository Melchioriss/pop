<?php

namespace PlayOrPay\Domain\Event;

use Assert\Assert;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use DomainException;
use Exception;
use Insideone\Package\Collection\Identifiable;
use Insideone\Package\EnumFramework\AmbiguousValueException;
use League\Period\Period;
use PlayOrPay\Domain\Contracts\Entity\AggregateInterface;
use PlayOrPay\Domain\Contracts\Entity\AggregateTrait;
use PlayOrPay\Domain\Contracts\Entity\OnUpdateEventListenerInterface;
use PlayOrPay\Domain\Event\DomainEvent\Event\PickPlayedStatusChanged;
use PlayOrPay\Domain\Event\DomainEvent\Event\ReviewAdded;
use PlayOrPay\Domain\Event\Exception\WrongParticipantException;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Domain\Game\Game;
use PlayOrPay\Domain\Steam\Group;
use PlayOrPay\Domain\User\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

class Event implements OnUpdateEventListenerInterface, AggregateInterface, Identifiable
{
    use AggregateTrait;

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

    /** @var DateTimeImmutable */
    private $createdAt;

    /** @var DateTimeImmutable|null */
    private $updatedAt;

    /** @var EventParticipant[]|ArrayCollection<int, EventParticipant> */
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
        $this->createdAt = new DateTimeImmutable();
        $this->participants = new ArrayCollection();
        $this->fillParticipants($group);
    }

    /**
     * @param User $user
     * @param UuidInterface|null $participantUuid
     *
     * @throws Exception
     *
     * @return EventParticipant
     */
    private function makeParticipant(User $user, UuidInterface $participantUuid = null): EventParticipant
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
     * @param Group $group
     *
     * @throws Exception
     */
    private function fillParticipants(Group $group): void
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
    public function generatePickers(): void
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
     * @param UuidInterface $needlePicker
     *
     * @throws NotFoundException
     *
     * @return EventPicker
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

        throw NotFoundException::forObject(EventPicker::class, $needlePicker->toString());
    }

    /**
     * @param UuidInterface $pickerUuid
     * @param User $newUser
     *
     * @throws NotFoundException
     *
     * @return Event
     */
    public function replacePickerUser(UuidInterface $pickerUuid, User $newUser): self
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @return EventParticipant[]
     */
    public function getParticipants(): array
    {
        return $this->participants->toArray();
    }

    /**
     * @param User $user
     * @param UuidInterface|null $participantUuid
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
        $users = $this->participants->map(function (EventParticipant $participant) {
            return $participant->getUser();
        })->toArray();

        foreach ($this->participants as $participant) {
            foreach ($participant->getPickers() as $picker) {
                $user = $picker->getUser();
                if (!in_array($user, $users)) {
                    $users[] = $user;
                }
            }
        }

        return $users;
    }

    public function onUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
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

    public function updateParticipantBlaeoGames(UuidInterface $participantUuid, string $blaeoGames): self
    {
        $participant = $this->getParticipant($participantUuid);
        $participant->updateBlaeoGames($blaeoGames);

        return $this;
    }

    public function updateParticipantGroupWins(UuidInterface $participantUuid, string $groupWins): self
    {
        $participant = $this->getParticipant($participantUuid);
        $participant->updateGroupWins($groupWins);

        return $this;
    }

    /**
     * @param UuidInterface $participantUuid
     * @param EventReward $blaeoGamesReward
     * @param int $blaeoPoins
     *
     * @throws Exception
     *
     * @return Event
     */
    public function updateParticipantBlaeoPoints(UuidInterface $participantUuid, EventReward $blaeoGamesReward, int $blaeoPoins): self
    {
        $participant = $this->getParticipant($participantUuid);
        $participant->updateBlaeoPoints($blaeoGamesReward, $blaeoPoins);

        return $this;
    }

    public function updateParticipantExtraRules(UuidInterface $participantUuid, string $extraRules): self
    {
        $participant = $this->getParticipant($participantUuid);
        $participant->updateExtraRules($extraRules);

        return $this;
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
        $participantUserCollection = new ArrayCollection();
        foreach ($this->getParticipants() as $participant) {
            $participantUserCollection->add($participant->getUser());
        }

        return array_filter($this->group->getMembers(), function (User $user) use ($participantUserCollection) {
            return !$participantUserCollection->contains($user);
        });
    }

    /**
     * @param UuidInterface $commentUuid
     * @param UuidInterface $pickerUuid
     * @param User $user
     * @param string $text
     * @param UuidInterface $referencedPickUuid
     * @param EventCommentGameReferenceType|null $gameReferenceType
     *
     * @throws AmbiguousValueException
     * @throws NotFoundException
     * @throws ReflectionException
     *
     * @return Event
     */
    public function addPickerComment(
        UuidInterface $commentUuid,
        UuidInterface $pickerUuid,
        User $user,
        string $text,
        ?UuidInterface $referencedPickUuid,
        ?EventCommentGameReferenceType $gameReferenceType
    ): self {
        $comment = $this
            ->getPicker($pickerUuid)
            ->addComment($commentUuid, $user, $text, $referencedPickUuid, $gameReferenceType);

        if ($comment->hasReferencedGame()) {
            switch ((int) (string) $comment->getGameReferenceType()) {
                case EventCommentGameReferenceType::REVIEW:
                    $this->addDomainEvent(new ReviewAdded($comment));

                    break;
                case EventCommentGameReferenceType::REPICK:
                    $this->rejectPick($comment->getPick()->getUuid());

                    break;
            }
        }

        return $this;
    }

    /**
     * @param UuidInterface $pickerUuid
     * @param UuidInterface $participantUuid
     * @param User $user
     * @param EventPickerType $pickerType
     *
     * @throws AmbiguousValueException
     * @throws ReflectionException
     *
     * @return $this
     */
    public function addPicker(
        UuidInterface $pickerUuid,
        UuidInterface $participantUuid,
        User $user,
        EventPickerType $pickerType
    ) {
        $picker = $this->makePicker($pickerUuid, $participantUuid, $user, $pickerType);
        $participant = $picker->getParticipant();
        $participant->addPicker($picker);

        return $this;
    }

    private function makePicker(UuidInterface $pickerUuid, UuidInterface $participantUuid, User $user, EventPickerType $pickerType): EventPicker
    {
        $participant = $this->getParticipant($participantUuid);

        return new EventPicker(
            $pickerUuid,
            $participant,
            $user,
            $pickerType
        );
    }

    /**
     * @param UuidInterface $pickUuid
     * @param EventPickPlayedStatus $newPlayedStatus
     *
     * @throws NotFoundException
     *
     * @return Event
     */
    public function changePickPlayedStatus(UuidInterface $pickUuid, EventPickPlayedStatus $newPlayedStatus): self
    {
        $pick = $this->getPick($pickUuid);

        $oldStatus = $pick->getPlayedStatus();
        $pick->changePlayedStatus($newPlayedStatus);
        $this->addDomainEvent(new PickPlayedStatusChanged($pick, $oldStatus, $newPlayedStatus));

        return $this;
    }

    private function findPick(UuidInterface $pickUuid): ?EventPick
    {
        foreach ($this->participants as $participant) {
            $pick = $participant->findPick($pickUuid);
            if ($pick) {
                return $pick;
            }
        }

        return null;
    }

    /**
     * @param UuidInterface $pickUuid
     *
     * @throws NotFoundException
     *
     * @return EventPick
     */
    private function getPick(UuidInterface $pickUuid): EventPick
    {
        $pick = $this->findPick($pickUuid);
        if (!$pick) {
            throw NotFoundException::forObject(EventPick::class, $pickUuid->toString());
        }

        return $pick;
    }

    /**
     * @param UuidInterface $pickUuid
     * @param Game $game
     *
     * @throws NotFoundException
     *
     * @return Event
     */
    public function changePickGame(UuidInterface $pickUuid, Game $game): self
    {
        $this->getPick($pickUuid)->getParticipant()->changePickGame($pickUuid, $game);

        return $this;
    }

    /**
     * @param UuidInterface $pickerUuid
     * @param UuidInterface $pickUuid
     * @param EventPickType $type
     * @param Game $game
     *
     * @throws NotFoundException
     *
     * @return EventPick
     */
    public function makePick(
        UuidInterface $pickerUuid,
        UuidInterface $pickUuid,
        EventPickType $type,
        Game $game
    ): EventPick {
        return $this
            ->getPicker($pickerUuid)
            ->getParticipant()
            ->makePick($pickerUuid, $pickUuid, $type, $game);
    }

    /**
     * Generate picks for input games
     * For testing purpose.
     *
     * @param Game[] $games
     *
     * @throws Exception
     */
    public function generatePicks(array $games): void
    {
        foreach ($this->participants as $participant) {
            foreach ($participant->getPickers() as $picker) {
                $pickTypes = EventPickType::getEnums();
                $pickedGames = array_splice($games, -$picker->getPickQuota());
                if (!$pickedGames) {
                    return;
                }

                foreach ($pickedGames as $pickIdx => $pickedGame) {
                    $picker->makePick(Uuid::uuid4(), $pickTypes[$pickIdx], $pickedGame);
                }
            }
        }
    }

    public function fetchReward(
        UuidInterface $participantUuid,
        RewardReason $reason,
        ?UuidInterface $pickUuid
    ): ?EventEarnedReward {
        $participant = $this->findParticipant($participantUuid);
        if (!$participant) {
            return null;
        }

        return $participant->findReward($reason, $pickUuid);
    }

    /**
     * @param UuidInterface $participantUuid
     * @param UuidInterface|null $pickUuid
     *
     * @return EventEarnedReward[]
     */
    public function fetchRewards(UuidInterface $participantUuid, ?UuidInterface $pickUuid): array
    {
        $participant = $this->findParticipant($participantUuid);
        if (!$participant) {
            return [];
        }

        return $this->getParticipant($participantUuid)->findRewardsOfPick($pickUuid);
    }

    /**
     * @param UuidInterface $pickUuid
     * @param EventReward[] $untouchableRewards
     *
     * @throws NotFoundException
     */
    public function removePickRewards(UuidInterface $pickUuid, array $untouchableRewards = []): void
    {
        $pick = $this->getPick($pickUuid);
        $participant = $pick->getParticipant();

        $toRemove = array_filter(
            $this->fetchRewards($pick->getParticipant()->getUuid(), $pick->getUuid()),
            function (EventEarnedReward $earnedReward) use ($untouchableRewards) {
                foreach ($untouchableRewards as $untouchableReward) {
                    if ($untouchableReward->getReason()->equalTo($earnedReward->getReason())) {
                        return false;
                    }
                }

                return true;
            }
        );

        $participant->removeRewards($toRemove);
    }

    /**
     * Adds those rewards from $rewards which weren't added earlier.
     *
     * @param UuidInterface $participantUuid
     * @param EventReward[] $rewards
     * @param UuidInterface $pickUuid
     *
     * @throws Exception
     */
    public function setupRewards(UuidInterface $participantUuid, array $rewards, ?UuidInterface $pickUuid): void
    {
        Assert::thatAll($rewards)->isInstanceOf(EventReward::class);

        $participant = $this->getParticipant($participantUuid);
        foreach ($rewards as $reward) {
            $participant->setupReward($reward, $pickUuid, null);
        }
    }

    /**
     * Completely rewriting rewards for a pick.
     *
     * @param UuidInterface $participantUuid
     * @param EventReward[] $rewards
     * @param UuidInterface $pickUuid
     *
     * @throws NotFoundException
     * @throws Exception
     */
    public function setupPickRewards(UuidInterface $participantUuid, array $rewards, UuidInterface $pickUuid): void
    {
        Assert::thatAll($rewards)->isInstanceOf(EventReward::class);
        $this->removePickRewards($pickUuid, $rewards);
        $this->setupRewards($participantUuid, $rewards, $pickUuid);
    }

    /**
     * @return EventPick[]
     */
    public function getPicks(): array
    {
        $picks = [];
        foreach ($this->getPickers() as $picker) {
            array_push($picks, ...$picker->getPicks());
        }

        return $picks;
    }

    /**
     * @return Game[]
     */
    public function getGames(): array
    {
        $games = new ArrayCollection();
        foreach ([$this->getPickedGames(), $this->getCommentedGames()] as $gameBatch) {
            foreach ($gameBatch as $game) {
                if ($games->contains($game)) {
                    continue;
                }

                $games->add($game);
            }
        }

        return $games->toArray();
    }

    /**
     * @return Game[]
     */
    private function getPickedGames(): array
    {
        $games = [];
        foreach ($this->getPicks() as $pick) {
            $games[] = $pick->getGame();
        }

        return $games;
    }

    /**
     * @return Game[]
     */
    private function getCommentedGames(): array
    {
        $games = [];
        foreach ($this->getComments() as $comment) {
            if (!$comment->hasReferencedGame()) {
                continue;
            }

            $games[] = $comment->getReferencedGame();
        }

        return $games;
    }

    /**
     * @return EventPickerComment[]
     */
    private function getComments(): array
    {
        $comments = [];
        foreach ($this->getPickers() as $picker) {
            array_push($comments, ...$picker->getComments());
        }

        return $comments;
    }

    /**
     * @param UuidInterface $pickUuid
     *
     * @throws NotFoundException
     *
     * @return Event
     */
    private function rejectPick(UuidInterface $pickUuid): self
    {
        $this->getPick($pickUuid)->reject();

        return $this;
    }

    public function getIdentity(): string
    {
        return $this->getUuid()->toString();
    }
}

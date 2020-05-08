<?php

namespace PlayOrPay\Domain\Steam;

use Doctrine\Common\Collections\ArrayCollection;
use PlayOrPay\Domain\Contracts\Entity\AggregateInterface;
use PlayOrPay\Domain\Contracts\Entity\AggregateTrait;
use PlayOrPay\Domain\Steam\DomainEvent\MemberAdded;
use PlayOrPay\Domain\Steam\DomainEvent\MemberRemoved;
use PlayOrPay\Domain\User\User;

class Group implements AggregateInterface
{
    use AggregateTrait;

    /** @var int */
    private $id;

    /** @var string */
    private $code;

    /** @var string */
    private $name;

    /** @var string */
    private $logoUrl;

    /** @var User[]|ArrayCollection<int, User> */
    private $members;

    public function __construct(int $id, string $code, string $name, string $logoUrl)
    {
        $this->id = $id;

        $this
            ->setCode($code)
            ->setName($name)
            ->setLogoUrl($logoUrl);

        $this->members = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLogoUrl(): string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(string $logoUrl): self
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    public function addMember(User $member): self
    {
        if ($this->hasUser($member)) {
            return $this;
        }

        $this->members->add($member);

        return $this;
    }

    public function removeMember(User $member): self
    {
        $this->members->removeElement($member);

        return $this;
    }

    public function clearMembers(): self
    {
        $this->members->clear();

        return $this;
    }

    /**
     * @return User[]
     */
    public function getMembers(): array
    {
        return $this->members->toArray();
    }

    public function hasUser(User $user): bool
    {
        return $this->members->contains($user);
    }

    /**
     * @return User[]
     */
    public function getActiveMembers(): array
    {
        return $this->members->filter(function (User $user) {
            return $user->isActive();
        })->toArray();
    }

    /**
     * @param User[] $actualMembers
     *
     * @return Group
     */
    public function updateMembers(array $actualMembers): self
    {
        $omitEvents = $this->members->count() === 0;

        /** @var User[]|ArrayCollection<int, User> $actualMembersCollection */
        $actualMembersCollection = new ArrayCollection($actualMembers);
        foreach ($this->members as $member) {
            if ($actualMembersCollection->contains($member)) {
                // still here
                continue;
            }

            // was removed
            $this->members->removeElement($member);
            if (!$omitEvents) {
                $this->addDomainEvent(new MemberRemoved((string) $this->id, (string) $member->getSteamId()));
            }
        }

        foreach ($actualMembersCollection as $newMember) {
            // was already here
            if ($this->members->contains($newMember)) {
                continue;
            }

            // new one
            $this->members->add($newMember);
            if (!$omitEvents) {
                $this->addDomainEvent(new MemberAdded((string) $this->id, (string) $newMember->getSteamId()));
            }
        }

        return $this;
    }
}

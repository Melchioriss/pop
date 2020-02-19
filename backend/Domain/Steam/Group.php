<?php

namespace PlayOrPay\Domain\Steam;

use Doctrine\Common\Collections\ArrayCollection;
use PlayOrPay\Domain\Contracts\Entity\AggregateInterface;
use PlayOrPay\Domain\User\User;

class Group implements AggregateInterface
{
    /** @var int */
    private $id;

    /** @var string */
    private $code;

    /** @var string */
    private $name;

    /** @var string */
    private $logoUrl;

    /** @var User[] */
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

    public function hasUser(User $user)
    {
        return $this->members->contains($user);
    }

    public function hasMemberOfId(SteamId $steamId): bool
    {
        $steamIdValue = (string) $steamId;

        return $this->members->exists(function (User $member) use ($steamIdValue) {
            return (string) $member->getSteamId() === $steamIdValue;
        });
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
}

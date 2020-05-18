<?php

namespace PlayOrPay\Domain\Steam\DomainEvent;

use Assert\Assert;
use PlayOrPay\Domain\Contracts\DomainEvent\DomainEventInterface;
use PlayOrPay\Domain\Steam\Group;
use PlayOrPay\Domain\User\User;

class MemberAdded implements DomainEventInterface
{
    /** @var string */
    public $group;

    /** @var string */
    public $member;

    public function __construct(string $group, string $member)
    {
        Assert::thatAll([$group, $member])->minLength(1);

        $this->group = $group;
        $this->member = $member;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize()
    {
        return [
            'group'  => $this->group,
            'member' => $this->member,
        ];
    }

    public static function refsMap(): array
    {
        return [
            'group'  => Group::class,
            'member' => User::class,
        ];
    }
}

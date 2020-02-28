<?php

namespace PlayOrPay\Domain\Steam;

use Assert\Assert;

class RemoteGroup
{
    /** @var int */
    public $id;

    /** @var string */
    public $code;

    /** @var string */
    public $name;

    /** @var string */
    public $logoUrl;

    /** @var int[] */
    public $members = [];

    public function __construct(int $id, string $code, string $name, string $logoUrl)
    {
        Assert::thatAll([$code, $name, $logoUrl])->minLength(1);
        Assert::that($id)->greaterOrEqualThan(1);

        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
        $this->logoUrl = $logoUrl;
    }

    public function addMember(int $memberId): void
    {
        $this->members[] = $memberId;
    }
}

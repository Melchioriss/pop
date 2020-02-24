<?php

namespace PlayOrPay\Application\Command\User\User\AddGroups;

use Assert\Assert;
use PlayOrPay\Domain\Steam\SteamId;

class AddUserGroupsCommand
{
    /** @var SteamId */
    public $steamId;

    /** @var string[] */
    public $groupNames;

    /**
     * @param string $steamId
     * @param string[] $groupNames
     */
    public function __construct(string $steamId, array $groupNames)
    {
        Assert::thatAll($groupNames)->string()->minLength(1);

        $this->steamId = new SteamId($steamId);
        $this->groupNames = $groupNames;
    }
}

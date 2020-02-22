<?php

namespace PlayOrPay\Application\Query\User\User\FindByProfileName;

use Assert\Assert;

class FindUserByProfileNameQuery
{
    /** @var string|null */
    private $profileName;

    public function __construct(?string $profileName)
    {
        if (!$profileName) {
            return;
        }

        Assert::that($profileName)->minLength(1);
        $this->profileName = $profileName;
    }

    public function getProfileName(): ?string
    {
        return $this->profileName;
    }
}

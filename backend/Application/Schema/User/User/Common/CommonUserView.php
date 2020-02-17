<?php

namespace PlayOrPay\Application\Schema\User\User\Common;

use PlayOrPay\Domain\Role\RoleName;

class CommonUserView
{
    /** @var string */
    public $steamId;

    /** @var string */
    public $profileName;

    /** @var string */
    public $profileUrl;

    /** @var string */
    public $avatar;

    /** @var RoleName[] */
    public $roles;

    /** @var bool */
    public $active;

    /** @var string|null */
    public $blaeoName;

    /** @var string */
    public $extraRules;

    /** @var bool */
    public $admin;
}

<?php

namespace PlayOrPay\Domain\Role;

use Ducks\Component\SplTypes\SplEnum;

class RoleName extends SplEnum
{
    const USER = 'USER';
    const ADMIN = 'ADMIN';
}

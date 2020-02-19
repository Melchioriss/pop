<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type;

use PlayOrPay\Domain\Role\RoleName;

class RoleEnumType extends StringEnumType
{
    public function getEnumClass(): string
    {
        return RoleName::class;
    }
}

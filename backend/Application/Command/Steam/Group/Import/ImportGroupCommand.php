<?php

namespace PlayOrPay\Application\Command\Steam\Group\Import;

use Assert\Assert;

class ImportGroupCommand
{
    /** @var string */
    public $code;

    /** @var bool */
    public $force;

    public function __construct(string $code, bool $force = false)
    {
        Assert::that($code)->minLength(1);

        $this->code = $code;
        $this->force = $force;
    }
}

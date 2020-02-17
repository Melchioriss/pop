<?php

namespace PlayOrPay\Application\Command\Steam\Group\Import;

use Assert\Assert;

class ImportGroupCommand
{
    /** @var string */
    private $code;

    public function __construct(string $code)
    {
        Assert::that($code)->minLength(1);

        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}

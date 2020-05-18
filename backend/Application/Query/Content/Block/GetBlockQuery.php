<?php

namespace PlayOrPay\Application\Query\Content\Block;

use Assert\Assert;

class GetBlockQuery
{
    /** @var string */
    public $code;

    public function __construct(string $code)
    {
        Assert::that($code)->minLength(1);
        $this->code = $code;
    }
}

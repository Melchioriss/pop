<?php

namespace PlayOrPay\Application\Command\Content\Block;

use Assert\Assert;

class PutBlockCommand
{
    /** @var string */
    public $code;

    /** @var string */
    public $content;

    public function __construct(string $code, string $content)
    {
        Assert::thatAll([$code, $content])->minLength(1);
        $this->code = $code;
        $this->content = $content;
    }
}

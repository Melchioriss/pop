<?php

namespace PlayOrPay\Application\Query;

use Assert\Assert;

class PaginatedQuery
{
    /** @var int */
    public $page = 1;

    /** @var int */
    public $perPage = 10;

    public function __construct(?int $page, ?int $perPage)
    {
        Assert::lazy()
            ->that($page)->nullOr()->min(1)
            ->that($perPage)->nullOr()->min(1)
            ->verifyNow();

        if ($page) {
            $this->page = $page;
        }
        if ($perPage) {
            $this->perPage = $perPage;
        }
    }
}

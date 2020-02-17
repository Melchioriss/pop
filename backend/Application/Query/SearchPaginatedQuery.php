<?php

namespace PlayOrPay\Application\Query;

use Assert\Assert;

class SearchPaginatedQuery extends PaginatedQuery
{
    /** @var string */
    public $query;

    public function __construct(string $q, ?int $page, ?int $perPage)
    {
        parent::__construct($page, $perPage);

        Assert::that($q)->minLength(1);
        $this->query = $q;

        if ($page) {
            $this->page = $page;
        }
        if ($perPage) {
            $this->perPage = $perPage;
        }
    }
}

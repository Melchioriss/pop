<?php

declare(strict_types=1);

namespace PlayOrPay\Application\Query;

use PlayOrPay\Domain\Exception\NotFoundException;

class Collection
{
    /** @var int */
    public $page;

    /** @var int */
    public $limit;

    /** @var int */
    public $total;

    /** @var int */
    public $pages;

    /**
     * @var object[]
     */
    public $data;

    /** @var array<string, object[]> */
    public $refs;

    /**
     * @param int $page
     * @param int $limit
     * @param int $total
     * @param object[] $data
     * @param array<string, object[]> $refs
     *
     * @throws NotFoundException
     */
    public function __construct(int $page, int $limit, int $total, array $data, array $refs = [])
    {
        $this->exists($page, $limit, $total);
        $this->page = $page;
        $this->limit = $limit;
        $this->total = $total;
        $this->data = $data;
        $this->refs = $refs;
        $this->calcPages();
    }

    /**
     * @param string $name
     * @param object[] $refs
     *
     * @return self
     */
    public function addRefs(string $name, array $refs): self
    {
        $this->refs[$name] = $refs;

        return $this;
    }

    private function calcPages(): void
    {
        $this->pages = $this->limit === 0 ? 1 : ceil($this->total / $this->limit);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param int $total
     *
     * @throws NotFoundException
     */
    private function exists(int $page, int $limit, int $total): void
    {
        if ($limit === 0 || $total === 0) {
            return;
        }

        if (($limit * ($page - 1)) >= $total) {
            throw new NotFoundException();
        }
    }
}

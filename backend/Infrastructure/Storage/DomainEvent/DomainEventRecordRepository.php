<?php

namespace PlayOrPay\Infrastructure\Storage\DomainEvent;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PlayOrPay\Application\Query\PaginatedQuery;
use PlayOrPay\Domain\DomainEvent\DomainEventRecord;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

class DomainEventRecordRepository extends ServiceEntityRepository
{
    public function getEntityClass(): string
    {
        return DomainEventRecord::class;
    }

    /**
     * @param PaginatedQuery $appQuery
     *
     * @return Paginator<DomainEventRecord>
     */
    public function getSortedList(PaginatedQuery $appQuery): Paginator
    {
        return $this->makePaginatedResult(
            $this
                ->createQueryBuilder('record')
                ->select('record')
                ->orderBy('record.createdAt', Criteria::DESC)
                ->getQuery(),
            $appQuery
        );
    }
}

<?php

namespace PlayOrPay\Infrastructure\Storage\DomainEvent;

use PlayOrPay\Domain\DomainEvent\DomainEventRecord;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

class DomainEventRecordRepository extends ServiceEntityRepository
{
    public function getEntityClass(): string
    {
        return DomainEventRecord::class;
    }
}

<?php

namespace PlayOrPay\Application\Query\DomainEvent\DomainEventRecord\GetAll;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\InvalidArgumentException;
use PlayOrPay\Application\Query\QueryHandlerInterface;
use PlayOrPay\Application\Schema\DomainEvent\Collection\CollectionDomainEventRecord;
use PlayOrPay\Application\Schema\DomainEvent\Collection\CollectionDomainEventRecordMappingConfigurator;
use PlayOrPay\Infrastructure\Storage\DomainEvent\DomainEventRecordRepository;

class GetAllDomainEventRecordsHandler implements QueryHandlerInterface
{
    /** @var DomainEventRecordRepository */
    private $domainEventRecordRepo;

    /** @var CollectionDomainEventRecordMappingConfigurator */
    private $mapping;

    public function __construct(
        DomainEventRecordRepository $domainEventRecordRepo,
        CollectionDomainEventRecordMappingConfigurator $mapping
    )
    {
        $this->domainEventRecordRepo = $domainEventRecordRepo;
        $this->mapping = $mapping;
    }

    /**
     * @param GetAllDomainEventRecordsQuery $query
     * @return array
     * @throws InvalidArgumentException
     */
    public function __invoke(GetAllDomainEventRecordsQuery $query)
    {
        $eventRecords = $this->domainEventRecordRepo->paginateAll($query);

        $this->mapping->configure($config = new AutoMapperConfig());

        return (new AutoMapper($config))->mapMultiple($eventRecords, CollectionDomainEventRecord::class);
    }
}

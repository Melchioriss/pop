<?php

namespace PlayOrPay\Application\Query\DomainEvent\DomainEventRecord\GetLog;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PlayOrPay\Application\Query\Collection;
use PlayOrPay\Application\Query\QueryHandlerInterface;
use PlayOrPay\Application\Schema\DomainEvent\Log\CollectionDomainEventRecord;
use PlayOrPay\Application\Schema\DomainEvent\Log\CollectionDomainEventRecordMappingConfigurator;
use PlayOrPay\Application\Schema\DomainEvent\Log\LogGame;
use PlayOrPay\Application\Schema\DomainEvent\Log\LogPick;
use PlayOrPay\Application\Schema\DomainEvent\Log\LogUser;
use PlayOrPay\Domain\Contracts\DomainEvent\DomainEventInterface;
use PlayOrPay\Domain\DomainEvent\DomainEventRecord;
use PlayOrPay\Domain\Event\EventPick;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Domain\Game\Game;
use PlayOrPay\Domain\User\User;
use PlayOrPay\Infrastructure\Storage\DomainEvent\DomainEventRecordRepository;

class GetDomainEventRecordsLogHandler implements QueryHandlerInterface
{
    /** @var DomainEventRecordRepository */
    private $domainEventRecordRepo;

    /** @var CollectionDomainEventRecordMappingConfigurator */
    private $mapping;

    /** @var EntityManagerInterface */
    private $em;

    const REFS_MAPPING = [
        Game::class => LogGame::class,
        EventPick::class => LogPick::class,
        User::class => LogUser::class,
    ];

    public function __construct(
        DomainEventRecordRepository $domainEventRecordRepo,
        CollectionDomainEventRecordMappingConfigurator $mapping,
        EntityManagerInterface $em
    )
    {
        $this->domainEventRecordRepo = $domainEventRecordRepo;
        $this->mapping = $mapping;
        $this->em = $em;
    }

    /**
     * @param GetDomainEventRecordsLogQuery $query
     * @return Collection
     *
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function __invoke(GetDomainEventRecordsLogQuery $query)
    {
        $this->mapping->configure($config = new AutoMapperConfig());

        /** @var DomainEventRecord[]|Paginator $eventRecords */
        $eventRecords = $this->domainEventRecordRepo->paginateAll($query);

        $refs = [];
        foreach ($eventRecords as $eventRecord) {
            /** @var DomainEventInterface $eventClass */
            $eventClass = $eventRecord->getName();

            $payload = $eventRecord->getPayload();
            foreach ($eventClass::refsMap() as $fieldName => $refClass) {
                if (empty($payload[$fieldName])) {
                    continue;
                }

                if (empty($refs[$refClass])) {
                    $refs[$refClass] = [];
                }

                $refs[$refClass][] = $payload[$fieldName];
            }
        }

        $mapper = (new AutoMapper($config));

        $collection = new Collection(
            $query->page,
            $query->perPage,
            $eventRecords->count(),
            $mapper->mapMultiple(
                $eventRecords->getIterator()->getArrayCopy(),
                CollectionDomainEventRecord::class
            )
        );

        foreach ($refs as $refClass => $classRefs) {
            if (empty(self::REFS_MAPPING[$refClass])) {
                continue;
            }

            $repo = $this->em->getRepository($refClass);
            $classMetadata = $this->em->getClassMetadata($refClass);

            $collection->addRefs(
                lcfirst($classMetadata->getReflectionClass()->getShortName()),
                $mapper->mapMultiple(
                    $repo->findBy([
                        $classMetadata->identifier[0] => $classRefs
                    ]),
                    self::REFS_MAPPING[$refClass]
                )
            );
        }

        return $collection;
    }
}

<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Repository;

use Assert\Assert;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use League\Tactician\CommandBus;
use PlayOrPay\Application\Query\PaginatedQuery;
use PlayOrPay\Domain\Contracts\Entity\AggregateInterface;
use PlayOrPay\Domain\DomainEvent\DomainEventRecord;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class ServiceEntityRepository extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository
{
    /** @var CommandBus */
    private $domainBus;

    /** @var ActorFinder */
    private $actorFinder;

    /**
     * Should tell us which class this repository serves.
     *
     * @return string
     */
    abstract public function getEntityClass(): string;

    public function __construct(
        ManagerRegistry $registry,
        CommandBus $domainBus,
        ActorFinder $actorFinder
    ) {
        parent::__construct($registry, $this->getEntityClass());
        $this->domainBus = $domainBus;
        $this->actorFinder = $actorFinder;
    }

    /**
     * @param UuidInterface|int|string $id
     * @param int|null $lockMode
     * @param int|null $lockVersion
     *
     * @throws EntityNotFoundException
     *
     * @return object
     */
    public function get($id, $lockMode = null, $lockVersion = null): object
    {
        $entity = $this->find($id, $lockMode, $lockVersion);
        if (!$entity) {
            throw EntityNotFoundException::fromClassNameAndIdentifier($this->getClassName(), [$id]);
        }

        return $entity;
    }

    public function isItForAnAggregate(): bool
    {
        return is_a($this->getEntityClass(), AggregateInterface::class, true);
    }

    /**
     * @param object ...$entities
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     * @throws Exception
     */
    public function save(object ...$entities): void
    {
        if (!$this->isItForAnAggregate()) {
            throw UnallowedOperationException::becauseSavingIsAvailableOnlyOnAggregate($this->getClassName());
        }

        Assert::thatAll($entities)->isInstanceOf($this->getEntityClass());

        foreach ($entities as $entity) {
            $this->_em->persist($entity);

            if ($entity instanceof AggregateInterface) {
                foreach ($entity->popDomainEvents() as $event) {
                    $eventRecord = DomainEventRecord::fromEvent($event, $this->actorFinder->findActor());
                    $this->_em->persist($eventRecord);

                    try {
                        $this->domainBus->handle($event);
                    } catch (Exception $e) {
                    }
                }
            }
        }
        $this->_em->flush();
    }

    /**
     * @param object $entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(object $entity): void
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }

    /**
     * @throws MappingException
     */
    public function clear(): void
    {
        $this->_em->clear();
    }

    /**
     * @throws Exception
     *
     * @return UuidInterface
     */
    public function nextUuid(): UuidInterface
    {
        return Uuid::uuid4();
    }

    /**
     * @param PaginatedQuery $appQuery
     *
     * @return Paginator<object>
     */
    public function paginateAll(PaginatedQuery $appQuery): Paginator
    {
        return $this->makePaginatedResult(
            $this
                ->createQueryBuilder('entity')
                ->select('entity')
                ->getQuery(),
            $appQuery
        );
    }

    /**
     * @param Query $dbQuery
     * @param PaginatedQuery $appQuery
     *
     * @return Paginator<mixed>
     */
    public function makePaginatedResult(Query $dbQuery, PaginatedQuery $appQuery): Paginator
    {
        $paginator = new Paginator($dbQuery);
        $paginator->getQuery()
            ->setFirstResult($appQuery->perPage * ($appQuery->page - 1))
            ->setMaxResults($appQuery->perPage);

        return $paginator;
    }
}

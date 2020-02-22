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
use PlayOrPay\Application\Query\PaginatedQuery;
use PlayOrPay\Domain\Contracts\Entity\AggregateInterface;
use PlayOrPay\Domain\DomainEvent\DomainEventRecord;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class ServiceEntityRepository extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

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
        EventDispatcherInterface $eventDispatcher,
        ActorFinder $actorFinder
    ) {
        parent::__construct($registry, $this->getEntityClass());
        $this->eventDispatcher = $eventDispatcher;
        $this->actorFinder = $actorFinder;
    }

    /**
     * @param $id
     * @param null $lockMode
     * @param null $lockVersion
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
    public function save(object ...$entities)
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

                    $this->eventDispatcher->dispatch($event);
                }
            }
        }
        $this->_em->flush();
    }

    /**
     * @throws MappingException
     */
    public function clear()
    {
        $this->_em->clear();
    }

    /**
     * @throws Exception
     *
     * @return UuidInterface
     */
    public function nextUuid()
    {
        return Uuid::uuid4();
    }

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

    public function makePaginatedResult(Query $dbQuery, PaginatedQuery $appQuery): Paginator
    {
        $paginator = new Paginator($dbQuery);
        $paginator->getQuery()
            ->setFirstResult($appQuery->perPage * ($appQuery->page - 1))
            ->setMaxResults($appQuery->perPage);

        return $paginator;
    }
}

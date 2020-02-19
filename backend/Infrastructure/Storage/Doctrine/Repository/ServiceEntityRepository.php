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
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class ServiceEntityRepository extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository
{
    /**
     * Should tell us which class this repository serves.
     *
     * @return string
     */
    abstract public function getEntityClass(): string;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->getEntityClass());
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
     */
    public function save(object ...$entities)
    {
        if (!$this->isItForAnAggregate()) {
            throw UnallowedOperationException::becauseSavingIsAvailableOnlyOnAggregate($this->getClassName());
        }

        Assert::thatAll($entities)->isInstanceOf($this->getEntityClass());

        foreach ($entities as $entity) {
            $this->_em->persist($entity);
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

    public function makePaginatedResult(Query $dbQuery, PaginatedQuery $appQuery): Paginator
    {
        $paginator = new Paginator($dbQuery);
        $paginator->getQuery()
            ->setFirstResult($appQuery->perPage * ($appQuery->page - 1))
            ->setMaxResults($appQuery->perPage);

        return $paginator;
    }
}

<?php

namespace PlayOrPay\Infrastructure\Storage\Steam;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PlayOrPay\Application\Query\SearchPaginatedQuery;
use PlayOrPay\Domain\Steam\Game;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

/**
 * @method Game get($id, $lockMode = null, $lockVersion = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function getEntityClass(): string
    {
        return Game::class;
    }

    public function getAllIds(): array
    {
        return array_map(
            'reset',
            $this->createQueryBuilder('game')
                ->select('game.id')
                ->getQuery()
                ->getScalarResult()
        );
    }

    public function findBySearch(SearchPaginatedQuery $querySpec): Paginator
    {
        $qb = $this->createQueryBuilder('game')
            ->select('game', 'case when game.name = :query then 1 else 0 end as hidden priority')
            ->setParameter('query', $querySpec->query)
            ->orderBy('priority', Criteria::DESC)
            ->addOrderBy('game.id', Criteria::ASC)
            ->andWhere('game.name like :likeQuery')
            ->setParameter('likeQuery', "%{$querySpec->query}%");

        if (is_numeric($querySpec->query)) {
            $qb
                ->orWhere('game.id = :gameId')
                ->setParameter('gameId', $querySpec->query)
            ;
        }

        return $this->makePaginatedResult($qb->getQuery(), $querySpec);
    }
}

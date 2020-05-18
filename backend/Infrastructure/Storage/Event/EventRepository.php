<?php

namespace PlayOrPay\Infrastructure\Storage\Event;

use DateTime;
use Exception;
use PlayOrPay\Domain\Event\Event;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

/**
 * @method Event get($id, $lockMode = null, $lockVersion = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function getEntityClass(): string
    {
        return Event::class;
    }

    /**
     * @throws Exception
     *
     * @return Event[]
     */
    public function allActive()
    {
        return $this->createQueryBuilder('event')
            ->where('DATE(event.activePeriod.startDate) <= DATE(:now)')
            ->andWhere('DATE(:now) <= DATE(event.activePeriod.endDate)')
            ->setParameter('now', new DateTime())
            ->getQuery()->getResult();
    }
}

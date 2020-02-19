<?php

namespace PlayOrPay\Application\Query\Event\Event\GetAll;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\InvalidArgumentException;
use Doctrine\Common\Collections\Criteria;
use PlayOrPay\Application\Query\QueryHandlerInterface;
use PlayOrPay\Application\Schema\Event\Event\Collection;
use PlayOrPay\Application\Schema\Event\Event\Collection\CollectionEventMappingConfigurator;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;

class GetAllEventsHandler implements QueryHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var CollectionEventMappingConfigurator */
    private $mapping;

    public function __construct(EventRepository $eventRepo, CollectionEventMappingConfigurator $mapping)
    {
        $this->eventRepo = $eventRepo;
        $this->mapping = $mapping;
    }

    /**
     * @param GetAllEventsQuery $query
     *
     * @throws InvalidArgumentException
     *
     * @return Collection\CollectionEventView[]
     */
    public function __invoke(GetAllEventsQuery $query): array
    {
        $domainEvents = $this->eventRepo->findBy([], [
            'activePeriod.endDate' => Criteria::DESC,
        ]);

        $this->mapping->configure($config = new AutoMapperConfig());

        return (new AutoMapper($config))->mapMultiple($domainEvents, Collection\CollectionEventView::class);
    }
}

<?php

namespace PlayOrPay\Application\Query\Event\Event\Get;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use Doctrine\ORM\EntityNotFoundException;
use PlayOrPay\Application\Query\QueryHandlerInterface;
use PlayOrPay\Application\Schema\Event\Event\Detail\DetailEventMappingConfigurator;
use PlayOrPay\Application\Schema\Event\Event\Detail\DetailEventView;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;

class GetEventHandler implements QueryHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var DetailEventMappingConfigurator */
    private $mapping;

    public function __construct(EventRepository $eventRepo, DetailEventMappingConfigurator $mapping)
    {
        $this->eventRepo = $eventRepo;
        $this->mapping = $mapping;
    }

    /**
     * @param GetEventQuery $query
     * @return mixed|null
     * @throws EntityNotFoundException
     * @throws UnregisteredMappingException
     */
    public function __invoke(GetEventQuery $query)
    {
        $event = $this->eventRepo->get($query->getUuid());

        $this->mapping->configure($config = new AutoMapperConfig);

        return (new AutoMapper($config))->map($event, DetailEventView::class);
    }
}

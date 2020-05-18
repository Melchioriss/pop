<?php

namespace PlayOrPay\Application\Query\Event\Event\Get;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\InvalidArgumentException;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use Doctrine\ORM\EntityNotFoundException;
use PlayOrPay\Application\Query\QueryHandlerInterface;
use PlayOrPay\Application\Schema\Event\Event\Detail\DetailEventMappingConfigurator;
use PlayOrPay\Application\Schema\Event\Event\Detail\DetailEventReward;
use PlayOrPay\Application\Schema\Event\Event\Detail\DetailEventView;
use PlayOrPay\Application\Schema\Event\Event\Detail\DetailGameView;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\Event\EventRewardRepository;

class GetEventHandler implements QueryHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var DetailEventMappingConfigurator */
    private $mapping;

    /** @var EventRewardRepository */
    private $eventRewardRepo;

    public function __construct(
        EventRepository $eventRepo,
        EventRewardRepository $eventRewardRepo,
        DetailEventMappingConfigurator $mapping
    ) {
        $this->eventRepo = $eventRepo;
        $this->eventRewardRepo = $eventRewardRepo;
        $this->mapping = $mapping;
    }

    /**
     * @param GetEventQuery $query
     *
     * @throws UnregisteredMappingException
     * @throws InvalidArgumentException
     * @throws EntityNotFoundException
     *
     * @return DetailEventView
     */
    public function __invoke(GetEventQuery $query): DetailEventView
    {
        $event = $this->eventRepo->get($query->getUuid());

        $this->mapping->configure($config = new AutoMapperConfig());

        $mapper = (new AutoMapper($config));

        /** @var DetailEventView $responseEvent */
        $responseEvent = $mapper->map($event, DetailEventView::class);

        $responseEvent->rewards = $mapper->mapMultiple($this->eventRewardRepo->findAll(), DetailEventReward::class);
        $responseEvent->games = $mapper->mapMultiple($event->getGames(), DetailGameView::class);

        return $responseEvent;
    }
}

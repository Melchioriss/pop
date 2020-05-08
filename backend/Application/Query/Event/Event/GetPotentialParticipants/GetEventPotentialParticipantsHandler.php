<?php

namespace PlayOrPay\Application\Query\Event\Event\GetPotentialParticipants;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityNotFoundException;
use PlayOrPay\Application\Query\QueryHandlerInterface;
use PlayOrPay\Application\Schema\User\User\PotentialParticipant\PotentialParticipantUserMappingConfigurator;
use PlayOrPay\Application\Schema\User\User\PotentialParticipant\PotentialParticipantUserView;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;

class GetEventPotentialParticipantsHandler implements QueryHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var PotentialParticipantUserMappingConfigurator */
    private $mapping;

    public function __construct(EventRepository $eventRepo, PotentialParticipantUserMappingConfigurator $mapping)
    {
        $this->eventRepo = $eventRepo;
        $this->mapping = $mapping;
    }

    /**
     * @param GetEventPotentialParticipantsQuery $query
     *
     * @throws InvalidArgumentException
     * @throws EntityNotFoundException
     *
     * @return PotentialParticipantUserView[]
     */
    public function __invoke(GetEventPotentialParticipantsQuery $query): array
    {
        $event = $this->eventRepo->get($query->eventUuid);

        $this->mapping->configure($config = new AutoMapperConfig());

        return (new AutoMapper($config))->mapMultiple(
            $event->getPotentialParticipants(),
            PotentialParticipantUserView::class
        );
    }
}

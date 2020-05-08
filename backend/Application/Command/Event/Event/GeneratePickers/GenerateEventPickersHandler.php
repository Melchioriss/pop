<?php

namespace PlayOrPay\Application\Command\Event\Event\GeneratePickers;

use Exception;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;

class GenerateEventPickersHandler implements CommandHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    public function __construct(EventRepository $eventRepo)
    {
        $this->eventRepo = $eventRepo;
    }

    /**
     * @param GenerateEventPickersCommand $command
     *
     * @throws Exception
     */
    public function __invoke(GenerateEventPickersCommand $command): void
    {
        $event = $this->eventRepo->get($command->getEventUuid());

        $event->generatePickers();

        $this->eventRepo->save($event);
    }
}

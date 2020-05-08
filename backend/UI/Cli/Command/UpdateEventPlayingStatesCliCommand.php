<?php

namespace PlayOrPay\UI\Cli\Command;

use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Insideone\Package\Collection\IdentifiableObjectSet;
use Insideone\Package\Collection\UnidentifiableObjectException;
use League\Tactician\CommandBus;
use PlayOrPay\Application\Command\Event\Event\ImportSteamPlaystats\ImportSteamPlayingStatesCommand;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateEventPlayingStatesCliCommand extends Command
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var CommandBus */
    private $commandBus;

    public function __construct(EventRepository $eventRepo, CommandBus $commandBus)
    {
        parent::__construct();
        $this->eventRepo = $eventRepo;
        $this->commandBus = $commandBus;
    }

    protected function configure(): void
    {
        $cmd = $this->setName('event:playing-states:update')->getDefinition();

        $cmd->addArguments([
            new InputArgument('event', InputArgument::OPTIONAL, 'An event UUID to update'),
        ]);

        $cmd->addOptions([
            new InputOption('active', null, InputOption::VALUE_NONE, 'Update all active events'),
        ]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws EntityNotFoundException
     * @throws UnidentifiableObjectException
     * @throws Exception
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ss = new SymfonyStyle($input, $output);

        if ($input->getOption('active')) {
            $events = $this->eventRepo->allActive();
        } else {
            $eventUuid = $input->getArgument('event');
            if (!$eventUuid) {
                $ss->error("You should specify either 'event' argument or 'active' option");

                return -1;
            }
            $events = [$this->eventRepo->get($eventUuid)];
        }

        $ss->note('Going to update these events: ');
        $ss->note((new IdentifiableObjectSet($events))->identities());

        foreach ($events as $event) {
            $this->commandBus->handle(new ImportSteamPlayingStatesCommand($event->getUuid()->toString()));
        }

        $ss->success('Finished');

        return 0;
    }
}

<?php

namespace PlayOrPay\UI\Cli\Command;

use League\Tactician\CommandBus;
use PlayOrPay\Application\Command\Event\Event\Delete\DeleteEventCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteEventCliCommand extends Command
{
    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        parent::__construct();

        $this->commandBus = $commandBus;
    }

    protected function configure(): void
    {
        $this
            ->setName('event:delete')
            ->getDefinition()
            ->addArguments([
                new InputArgument('eventUuid', InputArgument::REQUIRED, 'Event identifier to be deleted'),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ss = new SymfonyStyle($input, $output);

        $this->commandBus->handle(new DeleteEventCommand($input->getArgument('eventUuid')));

        $ss->success('OK');

        return 0;
    }
}

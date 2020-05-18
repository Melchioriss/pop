<?php

namespace PlayOrPay\UI\Cli\Command;

use League\Tactician\CommandBus;
use PlayOrPay\Application\Command\Steam\Game\ImportSteamGamesCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportSteamGamesCliCommand extends Command
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
            ->setName('steam:games:import')
            ->setDescription('Import games from Steam to the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ss = new SymfonyStyle($input, $output);

        $this->commandBus->handle(new ImportSteamGamesCommand());

        $ss->success('Games were successfully imported');

        return 0;
    }
}

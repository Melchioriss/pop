<?php

namespace PlayOrPay\UI\Cli\Command;

use League\Tactician\CommandBus;
use PlayOrPay\Application\Command\Steam\Group\Import\ImportGroupCommand;
use PlayOrPay\Domain\Exception\NotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportSteamGroupCliCommand extends Command
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
            ->setName('steam:group:import')
            ->setDescription('Imports steam group and its members to the site')
            ->getDefinition()
            ->addArguments([
                new InputArgument(
                    'groupCode',
                    InputArgument::REQUIRED,
                    'The group code you wanted to import'
                ),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ss = new SymfonyStyle($input, $output);

        $importGroupCommand = new ImportGroupCommand($input->getArgument('groupCode'));

        try {
            $this->commandBus->handle($importGroupCommand);
        } catch (NotFoundException $e) {
            $ss->error("Steam doesn't know such a group");

            return -1;
        }

        $ss->success('Group successfully imported');

        return 0;
    }
}

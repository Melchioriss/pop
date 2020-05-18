<?php

namespace PlayOrPay\UI\Cli\Command;

use League\Tactician\CommandBus;
use PlayOrPay\Application\Command\User\User\AddRoles\AddUserRolesCommand;
use PlayOrPay\Application\Query\User\User\FindByProfileName\FindUserByProfileNameQuery;
use PlayOrPay\Application\Schema\User\User\Common;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddUserRolesCliCommand extends Command
{
    /** @var CommandBus */
    private $queryBus;

    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $queryBus, CommandBus $commandBus)
    {
        parent::__construct();
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
    }

    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('user:roles:add')
            ->getDefinition()
            ->addArguments([
                new InputArgument('profileName', InputArgument::REQUIRED, 'Which user you want to be granted'),
                new InputArgument('roles', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Which roles you want to add'),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ss = new SymfonyStyle($input, $output);

        $userQuery = new FindUserByProfileNameQuery($input->getArgument('profileName'));

        /** @var Common\CommonUserView|null $user */
        $user = $this->queryBus->handle($userQuery);

        if (!$user) {
            $ss->error(sprintf("There is no user with profile name '%s'", $userQuery->getProfileName()));

            return -1;
        }

        $ss->note('User was found. Trying to grant them');

        $this->commandBus->handle(new AddUserRolesCommand((int) $user->steamId, $input->getArgument('roles')));

        $ss->success('Roles were successfully added');

        return 0;
    }
}

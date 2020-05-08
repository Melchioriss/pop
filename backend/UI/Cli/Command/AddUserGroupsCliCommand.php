<?php

namespace PlayOrPay\UI\Cli\Command;

use League\Tactician\CommandBus;
use PlayOrPay\Application\Command\User\User\AddGroups\AddUserGroupsCommand;
use PlayOrPay\Application\Query\User\User\FindByProfileName\FindUserByProfileNameQuery;
use PlayOrPay\Application\Schema\User\User\Common\CommonUserView;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddUserGroupsCliCommand extends Command
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
            ->setName('user:groups:add')
            ->getDefinition()
            ->addArguments([
                new InputArgument('profileName', InputArgument::REQUIRED, 'Which user you want to be added to the groups'),
                new InputArgument('groups', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Which groups you want to add'),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ss = new SymfonyStyle($input, $output);

        $userQuery = new FindUserByProfileNameQuery($input->getArgument('profileName'));

        /** @var CommonUserView|null $user */
        $user = $this->queryBus->handle($userQuery);

        if (!$user) {
            $ss->error(sprintf("There is no user with profile name '%s'", $userQuery->getProfileName()));

            return -1;
        }

        $ss->note('User was found. Trying to add them to the groups');

        $this->commandBus->handle(new AddUserGroupsCommand($user->steamId, $input->getArgument('groups')));

        $ss->success('Groups were successfully added');

        return 0;
    }
}

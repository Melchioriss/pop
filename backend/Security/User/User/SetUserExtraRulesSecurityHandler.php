<?php

namespace PlayOrPay\Security\User\User;

use Doctrine\ORM\EntityNotFoundException;
use PlayOrPay\Application\Command\User\User\SetExtraRules\SetUserExtraRulesCommand;
use PlayOrPay\Infrastructure\Storage\User\ActorFinder;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;
use PlayOrPay\Security\CommonSecurityHandler;
use PlayOrPay\Security\SecuriryException;

class SetUserExtraRulesSecurityHandler extends CommonSecurityHandler
{
    /** @var UserRepository */
    private $userRepo;

    public function __construct(ActorFinder $actorFinder, UserRepository $userRepo)
    {
        parent::__construct($actorFinder);
        $this->userRepo = $userRepo;
    }

    /**
     * @param SetUserExtraRulesCommand $command
     *
     * @throws EntityNotFoundException
     * @throws SecuriryException
     */
    public function __invoke(SetUserExtraRulesCommand $command): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $user = $this->userRepo->get($command->getSteamId());
        $this->assertActor($user);
    }
}

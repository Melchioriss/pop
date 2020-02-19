<?php

namespace PlayOrPay\Application\Command\Steam\Group\Import;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use GuzzleHttp\Exception\GuzzleException;
use Knojector\SteamAuthenticationBundle\Exception\InvalidApiResponseException;
use Knojector\SteamAuthenticationBundle\Exception\InvalidUserClassException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Steam\GroupRemoteRepository;
use PlayOrPay\Infrastructure\Storage\Steam\GroupRepository;

class ImportGroupHandler implements CommandHandlerInterface
{
    /** @var GroupRemoteRepository */
    private $groupRemoteRepo;

    /** @var GroupRepository */
    private $groupRepo;

    public function __construct(GroupRemoteRepository $groupRemoteRepo, GroupRepository $groupRepo)
    {
        $this->groupRemoteRepo = $groupRemoteRepo;
        $this->groupRepo = $groupRepo;
    }

    /**
     * @param ImportGroupCommand $command
     *
     * @throws GuzzleException
     * @throws InvalidApiResponseException
     * @throws InvalidUserClassException
     * @throws NotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     */
    public function __invoke(ImportGroupCommand $command)
    {
        $group = $this->groupRemoteRepo->findByCode($command->getCode());
        if (!$group) {
            throw new NotFoundException();
        }

        $this->groupRepo->save($group);
    }
}

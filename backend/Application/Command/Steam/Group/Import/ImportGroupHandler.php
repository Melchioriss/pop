<?php

namespace PlayOrPay\Application\Command\Steam\Group\Import;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Knojector\SteamAuthenticationBundle\Exception\InvalidApiResponseException;
use Knojector\SteamAuthenticationBundle\Exception\InvalidUserClassException;
use Knojector\SteamAuthenticationBundle\Factory\UserFactory;
use Knojector\SteamAuthenticationBundle\Http\SteamApiClient;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Steam\Group;
use PlayOrPay\Domain\Steam\SteamId;
use PlayOrPay\Domain\User\User;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Steam\Exception\RemoteNotFoundException;
use PlayOrPay\Infrastructure\Storage\Steam\GroupRemoteRepository;
use PlayOrPay\Infrastructure\Storage\Steam\GroupRepository;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

class ImportGroupHandler implements CommandHandlerInterface
{
    /** @var GroupRemoteRepository */
    private $groupRemoteRepo;

    /** @var GroupRepository */
    private $groupRepo;

    /** @var UserRepository */
    private $userRepo;

    /** @var SteamApiClient */
    private $profileClient;

    /** @var UserFactory */
    private $userFactory;

    public function __construct(
        GroupRemoteRepository $groupRemoteRepo,
        GroupRepository $groupRepo,
        UserRepository $userRepo,
        SteamApiClient $profileClient,
        UserFactory $userFactory
    ) {
        $this->groupRemoteRepo = $groupRemoteRepo;
        $this->groupRepo = $groupRepo;
        $this->userRepo = $userRepo;
        $this->profileClient = $profileClient;
        $this->userFactory = $userFactory;
    }

    /**
     * @param ImportGroupCommand $command
     *
     * @throws GuzzleException
     * @throws InvalidApiResponseException
     * @throws InvalidUserClassException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     * @throws RemoteNotFoundException
     * @throws Exception
     */
    public function __invoke(ImportGroupCommand $command): void
    {
        $remoteGroup = $this->groupRemoteRepo->getByCode($command->code);

        $actualMembers = [];
        foreach (array_chunk($remoteGroup->members, 100) as $members) {
            foreach ($this->profileClient->loadProfiles($members) as $member) {
                $user = $this->userRepo->find(new SteamId((int) $member['steamid']));
                if ($user) {
                    $user->update($member);
                } else {
                    /** @var User $user */
                    $user = $this->userFactory->getFromSteamApiResponse($member);
                }

                $actualMembers[] = $user;
            }
        }

        $localGroup = $this->groupRepo->find($remoteGroup->id);
        if (!$localGroup) {
            $localGroup = new Group($remoteGroup->id, $remoteGroup->code, $remoteGroup->name, $remoteGroup->logoUrl);
        }

        if (!$actualMembers && !$command->force) {
            throw new Exception('Empty members list was recieved. Updating is blocked until you use force');
        }

        $localGroup->updateMembers($actualMembers);

        $this->groupRepo->save($localGroup);
    }
}

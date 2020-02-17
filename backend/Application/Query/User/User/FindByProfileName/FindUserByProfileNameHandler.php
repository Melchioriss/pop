<?php

namespace PlayOrPay\Application\Query\User\User\FindByProfileName;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use PlayOrPay\Application\Query\QueryHandlerInterface;
use PlayOrPay\Application\Schema\User\User\Common\CommonUserMappingConfigurator;
use PlayOrPay\Domain\User\User;
use PlayOrPay\Application\Schema\User\User\Common;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FindUserByProfileNameHandler implements QueryHandlerInterface
{
    /** @var UserRepository */
    private $userRepo;

    /** @var CommonUserMappingConfigurator */
    private $mapping;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(UserRepository $userRepo, CommonUserMappingConfigurator $mapping, TokenStorageInterface $tokenStorage)
    {
        $this->userRepo = $userRepo;
        $this->mapping = $mapping;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param FindUserByProfileNameQuery $query
     * @return Common\CommonUserView|null
     * @throws UnregisteredMappingException
     */
    public function __invoke(FindUserByProfileNameQuery $query): ?Common\CommonUserView
    {
        $requestedProfileName = $query->getProfileName();
        if ($requestedProfileName) {
            /** @var User $user */
            $user = $this->userRepo->findOneBy(['profileName' => $query->getProfileName()]);
        } else {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        if (!$user instanceof User) {
            return null;
        }

        $this->mapping->configure($config = new AutoMapperConfig);
        return (new AutoMapper($config))->map($user, Common\CommonUserView::class);
    }
}

<?php

namespace PlayOrPay\Application\Query\User\User\GetAll;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\InvalidArgumentException;
use Doctrine\Common\Collections\Criteria;
use PlayOrPay\Application\Query\QueryHandlerInterface;
use PlayOrPay\Application\Schema\User\User\Common;
use PlayOrPay\Application\Schema\User\User\Common\CommonUserMappingConfigurator;
use PlayOrPay\Infrastructure\Storage\Steam\GroupRepository;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

class GetAllUsersHandler implements QueryHandlerInterface
{
    /** @var UserRepository */
    private $userRepo;

    /** @var CommonUserMappingConfigurator */
    private $mapping;
    /** @var GroupRepository */
    private $groupRepo;

    public function __construct(
        UserRepository $userRepo,
        CommonUserMappingConfigurator $mapping,
        GroupRepository $groupRepo
    ) {
        $this->userRepo = $userRepo;
        $this->mapping = $mapping;
        $this->groupRepo = $groupRepo;
    }

    /**
     * @param GetAllUsersQuery $query
     *
     * @throws InvalidArgumentException
     *
     * @return Common\CommonUserView[]
     */
    public function __invoke(GetAllUsersQuery $query): array
    {
        $groups = $this->groupRepo->findAll();

        $domainUsers = $this->userRepo->findBy([], [
            'active'  => Criteria::DESC,
            'steamId' => Criteria::ASC,
        ]);

        $outputUsers = [];
        foreach ($domainUsers as $user) {
            if ($user->getGroups() === $groups) {
                $outputUsers[] = $user;
            }
        }

        $this->mapping->configure($config = new AutoMapperConfig());

        return (new AutoMapper($config))->mapMultiple($outputUsers, Common\CommonUserView::class);
    }
}

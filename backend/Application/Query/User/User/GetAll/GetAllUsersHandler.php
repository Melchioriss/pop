<?php

namespace PlayOrPay\Application\Query\User\User\GetAll;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\InvalidArgumentException;
use Doctrine\Common\Collections\Criteria;
use PlayOrPay\Application\Query\QueryHandlerInterface;
use PlayOrPay\Application\Schema\User\User\Common;
use PlayOrPay\Application\Schema\User\User\Common\CommonUserMappingConfigurator;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;

class GetAllUsersHandler implements QueryHandlerInterface
{
    /** @var UserRepository */
    private $userRepo;

    /** @var CommonUserMappingConfigurator */
    private $mapping;

    public function __construct(UserRepository $userRepo, CommonUserMappingConfigurator $mapping)
    {
        $this->userRepo = $userRepo;
        $this->mapping = $mapping;
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
        $domainUsers = $this->userRepo->findBy([], [
            'active'  => Criteria::DESC,
            'steamId' => Criteria::ASC,
        ]);

        $this->mapping->configure($config = new AutoMapperConfig());

        return (new AutoMapper($config))->mapMultiple($domainUsers, Common\CommonUserView::class);
    }
}

<?php

namespace PlayOrPay\Application\Query\Steam\Group\GetAll;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\InvalidArgumentException;
use Doctrine\Common\Collections\Criteria;
use PlayOrPay\Application\Query\QueryHandlerInterface;
use PlayOrPay\Application\Schema\Steam\Group\Common;
use PlayOrPay\Application\Schema\Steam\Group\Common\CommonGroupMappingConfigurator;
use PlayOrPay\Infrastructure\Storage\Steam\GroupRepository;

class GetAllGroupsHandler implements QueryHandlerInterface
{
    /** @var GroupRepository */
    private $groupRepo;

    /** @var CommonGroupMappingConfigurator */
    private $mapping;

    public function __construct(GroupRepository $groupRepo, CommonGroupMappingConfigurator $mapping)
    {
        $this->groupRepo = $groupRepo;
        $this->mapping = $mapping;
    }

    /**
     * @param GetAllGroupsQuery $query
     *
     * @throws InvalidArgumentException
     *
     * @return Common\GroupView[]
     */
    public function __invoke(GetAllGroupsQuery $query): array
    {
        $domainGroups = $this->groupRepo->findBy([], [
            'id' => Criteria::DESC,
        ]);

        $this->mapping->configure($config = new AutoMapperConfig());

        return (new AutoMapper($config))->mapMultiple($domainGroups, Common\GroupView::class);
    }
}

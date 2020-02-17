<?php

namespace PlayOrPay\Application\Query\Steam\Game\GetList;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\InvalidArgumentException;
use PlayOrPay\Application\Query\Collection;
use PlayOrPay\Application\Query\QueryHandlerInterface;
use PlayOrPay\Application\Query\SearchPaginatedQuery;
use PlayOrPay\Application\Schema\Steam\Game\Common\CommonGameMappingConfigurator;
use PlayOrPay\Application\Schema\Steam\Game\Common;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Infrastructure\Storage\Steam\GameRepository;

class GetGameListHandler implements QueryHandlerInterface
{
    /** @var GameRepository */
    private $gameRepo;

    /** @var CommonGameMappingConfigurator */
    private $mapping;

    public function __construct(GameRepository $gameRepo, CommonGameMappingConfigurator $mapping)
    {
        $this->gameRepo = $gameRepo;
        $this->mapping = $mapping;
    }

    /**
     * @param SearchPaginatedQuery $query
     * @return Collection
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function __invoke(SearchPaginatedQuery $query): Collection
    {
        $gamesPaginator = $this->gameRepo->findBySearch($query);

        $this->mapping->configure($config = new AutoMapperConfig);

        /** @var Common\GameView[] $games */
        $games = (new AutoMapper($config))->mapMultiple(
            $gamesPaginator->getIterator()->getArrayCopy(),
            Common\GameView::class
        );

        return new Collection($query->page, $query->perPage, $gamesPaginator->count(), $games);
    }
}

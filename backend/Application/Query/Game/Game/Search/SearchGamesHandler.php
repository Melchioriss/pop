<?php

namespace PlayOrPay\Application\Query\Game\Game\Search;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\InvalidArgumentException;
use PlayOrPay\Application\Query\Collection;
use PlayOrPay\Application\Query\QueryHandlerInterface;
use PlayOrPay\Application\Schema\Game\Game\Common;
use PlayOrPay\Application\Schema\Game\Game\Common\CommonGameMappingConfigurator;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Infrastructure\Storage\Game\GameRepository;

class SearchGamesHandler implements QueryHandlerInterface
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
     * @param SearchGamesQuery $query
     *
     *@throws NotFoundException
     * @throws InvalidArgumentException
     *
     * @return Collection
     */
    public function __invoke(SearchGamesQuery $query): Collection
    {
        $gamesPaginator = $this->gameRepo->findBySearch($query);

        $this->mapping->configure($config = new AutoMapperConfig());

        /** @var Common\GameView[] $games */
        $games = (new AutoMapper($config))->mapMultiple(
            iterator_to_array($gamesPaginator->getIterator()),
            Common\GameView::class
        );

        return new Collection($query->page, $query->perPage, $gamesPaginator->count(), $games);
    }
}

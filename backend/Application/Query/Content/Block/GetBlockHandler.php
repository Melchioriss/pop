<?php

namespace PlayOrPay\Application\Query\Content\Block;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use Doctrine\ORM\EntityNotFoundException;
use PlayOrPay\Application\Query\QueryHandlerInterface;
use PlayOrPay\Application\Schema\Content\Block\BlockMappingConfigurator;
use PlayOrPay\Application\Schema\Content\Block\BlockView;
use PlayOrPay\Infrastructure\Storage\Content\BlockRepository;

class GetBlockHandler implements QueryHandlerInterface
{
    /** @var BlockRepository */
    private $blockRepo;

    /** @var BlockMappingConfigurator */
    private $mapping;

    public function __construct(BlockRepository $blockRepo, BlockMappingConfigurator $mapping)
    {
        $this->blockRepo = $blockRepo;
        $this->mapping = $mapping;
    }

    /**
     * @param GetBlockQuery $query
     *
     * @throws EntityNotFoundException
     * @throws UnregisteredMappingException
     *
     * @return BlockView
     */
    public function __invoke(GetBlockQuery $query): BlockView
    {
        $block = $this->blockRepo->get($query->code);

        $this->mapping->configure($config = new AutoMapperConfig());

        return (new AutoMapper($config))->map($block, BlockView::class);
    }
}

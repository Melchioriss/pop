<?php

namespace PlayOrPay\Application\Command\Content\Block;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Content\Block;
use PlayOrPay\Infrastructure\Storage\Content\BlockRepository;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;

class PutBlockHandler implements CommandHandlerInterface
{
    /** @var BlockRepository */
    private $blockRepo;

    public function __construct(BlockRepository $blockRepo)
    {
        $this->blockRepo = $blockRepo;
    }

    /**
     * @param PutBlockCommand $command
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     */
    public function __invoke(PutBlockCommand $command): void
    {
        $block = $this->blockRepo->find($command->code) ?: new Block($command->code, $command->content);
        $block->updateContent($command->content);
        $this->blockRepo->save($block);
    }
}

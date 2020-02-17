<?php

namespace PlayOrPay\Infrastructure\Storage\Content;

use PlayOrPay\Domain\Content\Block;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

/**
 * @method Block get($id, $lockMode = null, $lockVersion = null)
 */
class BlockRepository extends ServiceEntityRepository
{
    public function isSaveAllowed(): bool
    {
        return true;
    }

    public function getEntityClass(): string
    {
        return Block::class;
    }
}

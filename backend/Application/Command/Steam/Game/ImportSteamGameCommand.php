<?php

declare(strict_types=1);

namespace PlayOrPay\Application\Command\Steam\Game;

/**
 * Import a game by ID.
 */
class ImportSteamGameCommand
{
    /** @var int */
    public $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}

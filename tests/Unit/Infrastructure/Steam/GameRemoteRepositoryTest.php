<?php

namespace PlayOrPay\Tests\Unit\Infrastructure\Steam;

use PlayOrPay\Infrastructure\Storage\Steam\GameRemoteRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GameRemoteRepositoryTest extends KernelTestCase
{
    /**
     * @test
     */
    public function should_get_games_from_steam(): void
    {
        self::bootKernel();
        /** @var GameRemoteRepository $gameRemoteRepo */
        $gameRemoteRepo = self::$container->get(GameRemoteRepository::class);

        $this->assertNotCount(0, $gameRemoteRepo->getAll());
    }
}

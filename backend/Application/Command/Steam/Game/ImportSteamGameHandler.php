<?php

declare(strict_types=1);

namespace PlayOrPay\Application\Command\Steam\Game;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Game\Game;
use PlayOrPay\Domain\Game\GameId;
use PlayOrPay\Domain\Game\StoreId;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Game\GameRepository;
use PlayOrPay\Infrastructure\Storage\Steam\GameRemoteRepository;

/**
 * A handler to import steam game command.
 */
class ImportSteamGameHandler implements CommandHandlerInterface
{
    /** @var GameRepository */
    private $gameRepo;

    /** @var GameRemoteRepository */
    private $gameRemoteRepo;

    public function __construct(GameRepository $gameRepo, GameRemoteRepository $gameRemoteRepo)
    {
        $this->gameRepo = $gameRepo;
        $this->gameRemoteRepo = $gameRemoteRepo;
    }

    /**
     * @throws MappingException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     */
    public function __invoke(ImportSteamGameCommand $command): void
    {
        /** @var Game|null $existedGame */
        $existedGame = $this->gameRepo->findOneBy(['id.storeId' => $command->id]);

        $foundName = $this->gameRemoteRepo->getName($command->id);

        switch ($existedGame) {
            case null:
                $this->createGame($command->id, $foundName);

                break;
            default:
                $this->updateGame($existedGame, $foundName);
        }
    }

    /**
     * @throws MappingException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     */
    private function createGame(int $id, string $name): void
    {
        $newGame = new Game(new GameId(new StoreId(StoreId::STEAM), (string) $id), $name);
        $this->gameRepo->save($newGame);
        $this->gameRepo->clear();
    }

    /**
     * @throws MappingException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnallowedOperationException
     */
    private function updateGame(Game $existedGame, string $name): void
    {
        if ($existedGame->getName() === $name) {
            return;
        }

        $existedGame->updateName($name);

        $this->gameRepo->save($existedGame);
        $this->gameRepo->clear();
    }
}

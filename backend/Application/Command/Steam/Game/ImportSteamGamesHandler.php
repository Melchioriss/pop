<?php

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

class ImportSteamGamesHandler implements CommandHandlerInterface
{
    const CHUNK_SIZE = 3000;

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
     * @param ImportSteamGamesCommand $command
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws MappingException
     * @throws UnallowedOperationException
     */
    public function __invoke(ImportSteamGamesCommand $command): void
    {
        set_time_limit(600);

        $steamStoreId = new StoreId(StoreId::STEAM);

        $apps = $this->gameRemoteRepo->getAll();

        $counters = [
            'created' => 0,
            'updated' => 0,
        ];

        $existedAppIds = $this->gameRepo->getStoreIds(new StoreId(StoreId::STEAM));

        $newAppIds = array_diff(array_keys($apps), $existedAppIds);

        $existedAppIdsChunks = array_chunk(
            array_intersect(
                array_keys($apps),
                $existedAppIds
            ),
            self::CHUNK_SIZE
        );

        foreach ($existedAppIdsChunks as $existedChunk) {
            /** @var Game[] $existedGames */
            $existedGames = $this->gameRepo->findBy(['id.localId' => $existedChunk]);

            $updatedGames = [];

            foreach ($existedGames as $existedGame) {
                $gameId = $existedGame->getId()->getLocalId();

                $newName = $apps[$gameId]->name;

                if ($existedGame->getName() !== $newName) {
                    $existedGame->updateName($newName);
                    $updatedGames[] = $existedGame;
                    ++$counters['updated'];
                }
            }
            $this->gameRepo->save(...$updatedGames);
            $this->gameRepo->clear();
        }

        $newAppIdsChunks = array_chunk($newAppIds, self::CHUNK_SIZE);

        foreach ($newAppIdsChunks as $newChunk) {
            $newGames = [];
            foreach ($newChunk as $newId) {
                $app = $apps[$newId];
                $newGames[] = new Game(new GameId($steamStoreId, (string) $app->appid), $app->name);
                ++$counters['created'];
            }

            $this->gameRepo->save(...$newGames);
            $this->gameRepo->clear();
        }

        // TODO: show counters
        //return $counters;
    }
}

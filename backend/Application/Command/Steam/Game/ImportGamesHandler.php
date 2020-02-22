<?php

namespace PlayOrPay\Application\Command\Steam\Game;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Domain\Steam\Game;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Steam\GameRemoteRepository;
use PlayOrPay\Infrastructure\Storage\Steam\GameRepository;

class ImportGamesHandler implements CommandHandlerInterface
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
     * @param ImportGamesCommand $command
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws MappingException
     * @throws UnallowedOperationException
     */
    public function __invoke(ImportGamesCommand $command)
    {
        set_time_limit(120);

        $apps = $this->gameRemoteRepo->getAll();

        $counters = [
            'created' => 0,
            'updated' => 0,
        ];

        $existedAppIds = $this->gameRepo->getAllIds();

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
            $existedGames = $this->gameRepo->findBy(['id' => $existedChunk]);

            $updatedGames = [];

            foreach ($existedGames as $existedGame) {
                $gameId = $existedGame->getId();

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
                $newGames[] = new Game($app->appid, $app->name);
                ++$counters['created'];
            }

            $this->gameRepo->save(...$newGames);
            $this->gameRepo->clear();
        }

        // TODO: show counters
        //return $counters;
    }
}

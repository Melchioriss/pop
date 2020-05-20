<?php

namespace PlayOrPay\Application\Command\Event\Event\ImportSteamPlaystats;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Application\Query\Steam\PlayerService\GetOwnedGamesQuery;
use PlayOrPay\Application\Query\Steam\UserStatsQuery;
use PlayOrPay\Domain\Event\EventParticipant;
use PlayOrPay\Domain\Game\Game;
use PlayOrPay\Domain\Game\GameId;
use PlayOrPay\Domain\Game\StoreId;
use PlayOrPay\Infrastructure\Storage\Doctrine\Exception\UnallowedOperationException;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\Steam\Exception\UnexpectedResponseException;
use PlayOrPay\Infrastructure\Storage\Steam\OwnedGameRemoteRepository;
use PlayOrPay\Infrastructure\Storage\Steam\PlayerAchievementsRemoteRepository;
use PlayOrPay\Infrastructure\Storage\Steam\RecentlyPlayedRemoteRepository;

class ImportSteamPlayingStatesHandler implements CommandHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var OwnedGameRemoteRepository */
    private $ownedGameRemoteRepo;

    /** @var RecentlyPlayedRemoteRepository */
    private $recentlyPlayedRemoteRepository;

    /** @var PlayerAchievementsRemoteRepository */
    private $playerAchievementsRemoteRepo;

    /** @var StoreId */
    private $steamStore;

    public function __construct(
        EventRepository $eventRepo,
        OwnedGameRemoteRepository $ownedGameRemoteRepo,
        RecentlyPlayedRemoteRepository $recentlyPlayedRemoteRepository,
        PlayerAchievementsRemoteRepository $playerAchievementsRemoteRepo
    ) {
        $this->eventRepo = $eventRepo;
        $this->ownedGameRemoteRepo = $ownedGameRemoteRepo;
        $this->recentlyPlayedRemoteRepository = $recentlyPlayedRemoteRepository;
        $this->playerAchievementsRemoteRepo = $playerAchievementsRemoteRepo;
        $this->steamStore = new StoreId(StoreId::STEAM);
    }

    /**
     * @param ImportSteamPlayingStatesCommand $command
     *
     * @throws GuzzleException
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnexpectedResponseException
     * @throws UnallowedOperationException
     * @throws Exception
     */
    public function __invoke(ImportSteamPlayingStatesCommand $command): void
    {
        if (ini_get('max_execution_time') && !set_time_limit(0)) {
            throw new Exception("Can't prolog time limit");
        }

        $problems = [];

        $event = $this->eventRepo->get($command->getEventUuid());
        foreach ($event->getParticipants() as $participant) {
            try {
                $playerProblems = $this->updateParticipantPlayingStates($participant);
                $problems = array_merge($problems, $playerProblems);
            } catch (Exception $e) {
                $problems[] = $e;

                continue;
            }
        }

        $this->eventRepo->save($event);

        if ($problems) {
            $complexMessage = implode('; ', array_map(function (Exception $e) { return $e->getMessage(); }, $problems));

            throw new Exception($complexMessage, 0, count($problems) === 1 ? $problems[0] : null);
        }
    }

    /**
     * @param EventParticipant $participant
     *
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     *
     * @return Exception[]
     */
    private function updateParticipantPlayingStates(EventParticipant $participant): array
    {
        return array_merge(
            $this->updatePlaytime($participant),
            $this->updateAchievements($participant)
        );
    }

    /**
     * @param EventParticipant $participant
     *
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     *
     * @return Exception[]
     */
    private function updatePlaytime(EventParticipant $participant): array
    {
        $problems = [];

        $unresolvedPlaytimeGames = $this->makeUnresolvedMap($participant->getGames($this->steamStore));

        foreach ([
            function () use ($participant, $unresolvedPlaytimeGames) {
                $this->importPlaytimeFromGetOwned($participant, $unresolvedPlaytimeGames);
            },
            function () use ($participant, $unresolvedPlaytimeGames) {
                $this->importPlaytimeFromRecentlyPlayed($participant, $unresolvedPlaytimeGames);
            },
        ] as $operation) {
            try {
                $operation();
            } catch (Exception $e) {
                $problems[] = $e;
            }
        }

        return $problems;
    }

    /**
     * @param EventParticipant $participant
     *
     * @throws UnexpectedResponseException
     * @throws GuzzleException
     *
     * @return Exception[]
     */
    private function updateAchievements(EventParticipant $participant): array
    {
        $problems = [];
        foreach ($participant->getGames($this->steamStore) as $game) {
            try {
                $steamGameId = $game->getId()->getLocalId();
                $userStatsQuery = new UserStatsQuery((int) (string) $participant->getUserSteamId(), (int) $steamGameId);
                $achievementsState = $this->playerAchievementsRemoteRepo->find($userStatsQuery);

                $totalGameAchievementsCount = $achievementsState->count();
                if ($totalGameAchievementsCount) {
                    $this->updateGameAchievements($game, $achievementsState->count());
                }

                $participant->updateAchievementsForGame($game->getId(), $achievementsState->countAchieved());
            } catch (Exception $e) {
                $problems[] = $e;

                continue;
            }
        }

        return $problems;
    }

    /**
     * TODO: It's simpler to keep it here though it's not import playing states concern.
     *
     * @param Game     $game
     * @param int|null $achievements
     */
    private function updateGameAchievements(Game $game, ?int $achievements): void
    {
        $game->updateAchievements($achievements);
    }

    /**
     * @param Game[] $games
     *
     * @return array<int, Game>
     */
    private function makeUnresolvedMap(array $games): array
    {
        return array_combine(
            array_map(function (Game $game) { return $game->getId(); }, $games),
            $games
        );
    }

    /**
     * @param EventParticipant $participant
     * @param array<int, Game> $unresolvedGames
     *
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     */
    private function importPlaytimeFromGetOwned(EventParticipant $participant, array &$unresolvedGames): void
    {
        if (!$unresolvedGames || !$participant->hasPicks()) {
            return;
        }

        $getOwnedGamesQuery = $this->makeQuery($participant->getUser()->getSteamId(), $participant->getLocalGameIds($this->steamStore));
        foreach ($this->ownedGameRemoteRepo->find($getOwnedGamesQuery) as $ownedGame) {
            $participant->updatePlaytimeForGame(new GameId($this->steamStore, (string) $ownedGame->appId), $ownedGame->playtimeForever);
            unset($unresolvedGames[$ownedGame->appId]);
        }
    }

    /**
     * @param EventParticipant $participant
     * @param array<int, Game> $unresolvedGames
     *
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     */
    private function importPlaytimeFromRecentlyPlayed(EventParticipant $participant, array &$unresolvedGames): void
    {
        if (!$unresolvedGames || !$participant->hasPicks()) {
            return;
        }

        $recentlyPlayedList = $this->recentlyPlayedRemoteRepository->findBySteamId(
            (int) (string) $participant->getUserSteamId()
        );

        foreach ($recentlyPlayedList as $recentlyPlayedGame) {
            if (!array_key_exists($recentlyPlayedGame->appId, $unresolvedGames)) {
                continue;
            }

            $participant->updatePlaytimeForGame(
                new GameId($this->steamStore, (string) $recentlyPlayedGame->appId),
                $recentlyPlayedGame->playtimeForever
            );
            unset($unresolvedGames[$recentlyPlayedGame->appId]);
        }
    }

    /**
     * @param int $steamId
     * @param int[]|string[] $apps
     *
     * @return GetOwnedGamesQuery
     */
    private function makeQuery(int $steamId, array $apps): GetOwnedGamesQuery
    {
        $getOwnedGamesQuery = new GetOwnedGamesQuery($steamId);

        return $getOwnedGamesQuery
            ->includePlayedFreeGames()
            ->includeAppInfo()
            ->forApps($apps)
        ;
    }
}

<?php

namespace PlayOrPay\Application\Command\Event\Event\ImportPlaystats;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use GuzzleHttp\Exception\GuzzleException;
use PlayOrPay\Application\Command\CommandHandlerInterface;
use PlayOrPay\Application\Query\Steam\PlayerService\GetOwnedGamesQuery;
use PlayOrPay\Application\Query\Steam\UserStatsQuery;
use PlayOrPay\Domain\Event\EventParticipant;
use PlayOrPay\Domain\Steam\Game;
use PlayOrPay\Infrastructure\Storage\Event\EventRepository;
use PlayOrPay\Infrastructure\Storage\Steam\Exception\UnexpectedResponseException;
use PlayOrPay\Infrastructure\Storage\Steam\OwnedGameRemoteRepository;
use PlayOrPay\Infrastructure\Storage\Steam\PlayerAchievementsRemoteRepository;
use PlayOrPay\Infrastructure\Storage\Steam\RecentlyPlayedRemoteRepository;

class ImportPlayingStatesHandler implements CommandHandlerInterface
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var OwnedGameRemoteRepository */
    private $ownedGameRemoteRepo;

    /** @var RecentlyPlayedRemoteRepository */
    private $recentlyPlayedRemoteRepository;

    /** @var PlayerAchievementsRemoteRepository */
    private $playerAchievementsRemoteRepo;

    public function __construct(
        EventRepository $eventRepo,
        OwnedGameRemoteRepository $ownedGameRemoteRepo,
        RecentlyPlayedRemoteRepository $recentlyPlayedRemoteRepository,
        PlayerAchievementsRemoteRepository $playerAchievementsRemoteRepo
    )
    {
        $this->eventRepo = $eventRepo;
        $this->ownedGameRemoteRepo = $ownedGameRemoteRepo;
        $this->recentlyPlayedRemoteRepository = $recentlyPlayedRemoteRepository;
        $this->playerAchievementsRemoteRepo = $playerAchievementsRemoteRepo;
    }

    /**
     * @param ImportPlayingStatesCommand $command
     * @throws GuzzleException
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UnexpectedResponseException
     */
    public function __invoke(ImportPlayingStatesCommand $command)
    {
        $event = $this->eventRepo->get($command->getEventUuid());
        foreach ($event->getParticipants() as $participant) {
            $this->updateParticipantPlayingStates($participant);
        }

        $this->eventRepo->save($event);
    }

    /**
     * @param EventParticipant $participant
     * @return self
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     */
    private function updateParticipantPlayingStates(EventParticipant $participant): self
    {
        return $this
            ->updatePlaytime($participant)
            ->updateAchievements($participant);
    }

    /**
     * @param EventParticipant $participant
     * @return self
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     */
    private function updatePlaytime(EventParticipant $participant): self
    {
        $unresolvedPlaytimeGames = $this->makeUnresolvedMap($participant->getGames());
        $this->importPlaytimeFromGetOwned($participant, $unresolvedPlaytimeGames);
        $this->importPlaytimeFromRecentlyPlayed($participant, $unresolvedPlaytimeGames);
        return $this;
    }

    /**
     * @param EventParticipant $participant
     * @return ImportPlayingStatesHandler
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     */
    private function updateAchievements(EventParticipant $participant): self
    {
        foreach ($participant->getGames() as $game) {
            $userStatsQuery = new UserStatsQuery((int)(string)$participant->getUserSteamId(), $game->getId());
            $achievementsState = $this->playerAchievementsRemoteRepo->find($userStatsQuery);

            $totalGameAchievementsCount = $achievementsState->count();
            if ($totalGameAchievementsCount)
                $this->updateGameAchievements($game, $achievementsState->count());

            $participant->updateAchievementsForGame($game->getId(), $achievementsState->countAchieved());
        }

        return $this;
    }

    /**
     * TODO: It's simpler to keep it here though it's not import playing states concern
     * @param Game $game
     * @param int $achievements
     */
    private function updateGameAchievements(Game $game, ?int $achievements)
    {
        $game->updateAchievements($achievements);
    }

    private function makeUnresolvedMap(array $games): array
    {
        return array_combine(
            array_map(function (Game $game) { return $game->getId(); }, $games),
            $games
        );
    }

    /**
     * @param EventParticipant $participant
     * @param Game[] $unresolvedGames
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     */
    private function importPlaytimeFromGetOwned(EventParticipant $participant, array &$unresolvedGames)
    {
        if (!$unresolvedGames || !$participant->hasPicks()) {
            return;
        }

        $getOwnedGamesQuery = $this->makeQuery($participant->getUser()->getSteamId(), $participant->getGameIds());
        foreach ($this->ownedGameRemoteRepo->find($getOwnedGamesQuery) as $ownedGame) {
            $participant->updatePlaytimeForGame($ownedGame->appId, $ownedGame->playtimeForever);
            unset($unresolvedGames[$ownedGame->appId]);
        }
    }

    /**
     * @param EventParticipant $participant
     * @param array $unresolvedGames
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     */
    private function importPlaytimeFromRecentlyPlayed(EventParticipant $participant, array &$unresolvedGames)
    {
        if (!$unresolvedGames || !$participant->hasPicks()) {
            return;
        }

        $recentlyPlayedList = $this->recentlyPlayedRemoteRepository->findBySteamId(
            (int)(string)$participant->getUserSteamId()
        );

        foreach ($recentlyPlayedList as $recentlyPlayedGame) {
            if (!array_key_exists($recentlyPlayedGame->appId, $unresolvedGames)) {
                continue;
            }

            $participant->updatePlaytimeForGame($recentlyPlayedGame->appId, $recentlyPlayedGame->playtimeForever);
            unset($unresolvedGames[$recentlyPlayedGame->appId]);
        }
    }

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

<?php

namespace PlayOrPay\Infrastructure\Storage\Steam;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PlayOrPay\Application\Query\Steam\UserStatsQuery;
use PlayOrPay\Domain\Steam\PlayerAchievement;
use PlayOrPay\Domain\Steam\PlayerAchievementsResponse;
use Symfony\Component\HttpFoundation\Request;

class PlayerAchievementsRemoteRepository
{
    /** @var string */
    private $steamApiKey;

    /** @var ClientInterface */
    private $httpClient;

    /** @var string */
    private $endpoint = 'https://api.steampowered.com/ISteamUserStats/GetPlayerAchievements/v1/';

    public function __construct(string $steamApiKey, ClientInterface $httpClient)
    {
        $this->steamApiKey = $steamApiKey;
        $this->httpClient = $httpClient;
    }

    /**
     * @param UserStatsQuery $query
     *
     * @throws GuzzleException
     *
     * @return PlayerAchievementsResponse
     */
    public function find(UserStatsQuery $query): PlayerAchievementsResponse
    {
        $response = $this->httpClient->request(
            Request::METHOD_GET,
            $this->endpoint . '?' . http_build_query([
                'key'     => $this->steamApiKey,
                'steamId' => (int) (string) $query->steamId,
                'appid'   => $query->appId,
            ]),
            ['http_errors' => false]
        );

        $responseContent = $response->getBody()->getContents();
        $responseData = json_decode($responseContent, true);

        // special achievements-less case
        if (!empty($responseData['playerstats']['error'])
            && $responseData['playerstats']['error'] === 'Requested app has no stats') {
            return new PlayerAchievementsResponse([]);
        }

        $achievements = [];

        // ?? [] is for achievements-less games
        foreach ($responseData['playerstats']['achievements'] ?? [] as $achievement) {
            $achievements[] = new PlayerAchievement(
                $query->appId,
                $achievement['achieved']
            );
        }

        return new PlayerAchievementsResponse($achievements);
    }
}

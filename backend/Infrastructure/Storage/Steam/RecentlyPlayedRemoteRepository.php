<?php

namespace PlayOrPay\Infrastructure\Storage\Steam;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PlayOrPay\Domain\Steam\RecentlyPlayedGame;
use PlayOrPay\Infrastructure\Storage\Steam\Exception\UnexpectedResponseException;
use Symfony\Component\HttpFoundation\Request;

class RecentlyPlayedRemoteRepository
{
    /** @var string */
    private $steamApiKey;

    /** @var ClientInterface */
    private $httpClient;

    /** @var string */
    private $endpoint = 'https://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v1/';

    public function __construct(string $steamApiKey, ClientInterface $httpClient)
    {
        $this->steamApiKey = $steamApiKey;
        $this->httpClient = $httpClient;
    }

    /**
     * @param int $steamId
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     * @return RecentlyPlayedGame[]
     */
    public function findBySteamId(int $steamId): array
    {
        $response = $this->httpClient->request(
            Request::METHOD_GET,
            $this->endpoint.'?'.http_build_query([
                'key' => $this->steamApiKey,
                'steamId' => $steamId,
            ])
        );

        $responseContent = $response->getBody()->getContents();
        $responseData = json_decode($responseContent, true);

        if (!array_key_exists('games', $responseData['response'])) {
            throw UnexpectedResponseException::becauseFieldDoentExists('games');
        }

        $recentlyPlayedGames = [];
        foreach ($responseData['response']['games'] as $recentlyPlayedGame) {
            $recentlyPlayedGames[] = new RecentlyPlayedGame(
                $recentlyPlayedGame['appid'],
                $recentlyPlayedGame['playtime_forever']
            );
        }

        return $recentlyPlayedGames;
    }
}

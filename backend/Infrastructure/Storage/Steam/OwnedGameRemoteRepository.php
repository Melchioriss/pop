<?php

namespace PlayOrPay\Infrastructure\Storage\Steam;

use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PlayOrPay\Application\Query\Steam\PlayerService\GetOwnedGamesQuery;
use PlayOrPay\Domain\Steam\OwnedGame;
use PlayOrPay\Infrastructure\Storage\Steam\Exception\UnexpectedResponseException;
use Symfony\Component\HttpFoundation\Request;

class OwnedGameRemoteRepository
{
    /** @var string */
    private $steamApiKey;

    /** @var string */
    private $endpoint = 'https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/';

    /** @var ClientInterface */
    private $httpClient;

    public function __construct(string $steamApiKey, ClientInterface $httpClient)
    {
        $this->steamApiKey = $steamApiKey;
        $this->httpClient = $httpClient;
    }

    /**
     * @param GetOwnedGamesQuery $query
     *
     * @throws Exception
     * @throws UnexpectedResponseException
     * @throws GuzzleException
     *
     * @return OwnedGame[]
     */
    public function find(GetOwnedGamesQuery $query): array
    {
        $httpParams = [
            'steamid' => (string) $query->getSteamId(),
        ];

        if ($query->appInfoIncluded()) {
            $httpParams['include_appinfo'] = 1;
        }

        if ($query->playedFreeGamesIncluded()) {
            $httpParams['include_played_free_games'] = 1;
        }

        if ($apps = $query->getAppIdsFilter()) {
            $httpParams['appids_filter'] = $apps;
        }

        $response = $this->httpClient->request(
            Request::METHOD_GET,
            "{$this->endpoint}?" . http_build_query(['key' => $this->steamApiKey] + $httpParams)
        );
        $responseBody = $response->getBody()->getContents();

        $responseData = json_decode($responseBody, true);
        if (!array_key_exists('games', $responseData['response'])) {
            throw UnexpectedResponseException::becauseFieldDoesntExist('games', $httpParams);
        }

        $ownedGames = [];
        foreach ($responseData['response']['games'] as $responseOwnedGame) {
            $ownedGames[] = new OwnedGame(
                $responseOwnedGame['name'] ?? null,
                $responseOwnedGame['appid'],
                $responseOwnedGame['playtime_forever']
            );
        }

        return $ownedGames;
    }
}

<?php

namespace PlayOrPay\Infrastructure\Storage\Steam;

use Exception;
use Goutte\Client;

class GameRemoteRepository
{
    const BASE_URL = 'https://api.steampowered.com/IStoreService/GetAppList/v1/';

    /** @var string */
    private $key;

    /** @var Client */
    private $goutteClient;

    public function __construct(Client $goutteClient, string $steamApiKey)
    {
        $this->goutteClient = $goutteClient;
        $this->key = $steamApiKey;
    }

    /**
     * @return SteamAppSchema[]
     */
    public function getAll(): array
    {
        $games = [];

        $baseParams = [
            'key'           => $this->key,
            'include_games' => 1,
            'max_results'   => 45000,
        ];

        $lastAppId = 0;

        do {
            $urlParams = $baseParams;
            if ($lastAppId) {
                $urlParams['last_appid'] = $lastAppId;
            }

            $url = self::BASE_URL . '?' . urldecode(http_build_query($urlParams));
            $this->goutteClient->request('GET', $url);
            $response = json_decode($this->goutteClient->getResponse()->getContent(), true);
            $actualResponse = $response['response'];
            $lastAppId = isset($actualResponse['last_appid']) ? $actualResponse['last_appid'] : 0;

            if (!empty($actualResponse['apps'])) {
                foreach ($actualResponse['apps'] as $rawApp) {
                    $app = new SteamAppSchema();
                    $app->appid = $rawApp['appid'];
                    $app->name = htmlspecialchars_decode($rawApp['name']);
                    $games[] = $app;
                }
            }
        } while ($lastAppId);

        $gameIds = array_map(
            function ($app) {
                return $app->appid;
            },
            $games
        );

        $games = array_combine($gameIds, $games);

        return $games;
    }

    public function getName(int $id): string
    {
        $this->goutteClient->request(
            'GET',
            'https://steamspy.com/api.php?' . http_build_query(['request' => 'appdetails', 'appid' => $id])
        );

        $jsonResponse = $this->goutteClient->getResponse()->getContent();
        $assocResponse = json_decode($jsonResponse, true);

        if ((array_key_exists('name', $assocResponse) && is_string($assocResponse['name']))) {
            return $assocResponse['name'];
        }

        throw new Exception(sprintf("SteamSpy doesn't know about this ID [%d].", $id));
    }
}

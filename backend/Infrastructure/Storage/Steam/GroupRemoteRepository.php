<?php

/** @noinspection CssInvalidHtmlTagReference */

namespace PlayOrPay\Infrastructure\Storage\Steam;

use Assert\Assert;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PlayOrPay\Domain\Steam\RemoteGroup;
use PlayOrPay\Infrastructure\Storage\Steam\Exception\RemoteNotFoundException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

class GroupRemoteRepository
{
    const BASE_URL = 'https://steamcommunity.com/groups/%s/memberslistxml/?xml=1';

    /** @var ClientInterface */
    private $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $code
     *
     * @throws GuzzleException
     * @throws RemoteNotFoundException
     *
     * @return RemoteGroup
     */
    public function getByCode(string $code): RemoteGroup
    {
        Assert::that($code)->minLength(1);

        $url = sprintf(self::BASE_URL, $code);

        $groupXml = $this->httpClient->request(Request::METHOD_GET, $url)->getBody()->getContents();

        $groupData = new Crawler($groupXml);

        $groupIdNode = $groupData->filter('groupID64')->first();

        if ($groupIdNode->count() === 0) {
            throw RemoteNotFoundException::forQuery(RemoteGroup::class, compact('code'));
        }

        $group = new RemoteGroup(
            (int) $groupIdNode->text(),
            $groupData->filter('groupURL')->first()->text(),
            $groupData->filter('groupName')->first()->text(),
            $groupData->filter('avatarFull')->first()->text()
        );

        $groupData->filter('members steamID64')->each(function (Crawler $member) use (&$group) {
            $group->addMember((int) $member->text());
        });

        return $group;
    }
}

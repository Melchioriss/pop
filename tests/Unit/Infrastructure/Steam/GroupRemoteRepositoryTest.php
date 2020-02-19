<?php

namespace PlayOrPay\Tests\Unit\Infrastructure\Steam;

use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Knojector\SteamAuthenticationBundle\Exception\InvalidApiResponseException;
use Knojector\SteamAuthenticationBundle\Exception\InvalidUserClassException;
use Knojector\SteamAuthenticationBundle\Factory\UserFactory;
use Knojector\SteamAuthenticationBundle\Http\SteamApiClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PlayOrPay\Domain\User\User;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;
use PlayOrPay\Infrastructure\Storage\Steam\GroupRemoteRepository;
use PlayOrPay\Infrastructure\Storage\Steam\GroupRepository;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GroupRemoteRepositoryTest extends TestCase
{
    /**
     * @test
     * @group unit
     *
     * @throws GuzzleException
     * @throws InvalidApiResponseException
     * @throws InvalidUserClassException
     * @throws Exception
     */
    public function should_return_expected_group(): void
    {
        /** @var GroupRepository $groupRepo */
        $groupRepo = $this->getEmptyRepo(GroupRepository::class);

        /** @var UserRepository $userRepo */
        $userRepo = $this->getEmptyRepo(UserRepository::class);

        $repo = new GroupRemoteRepository(
            $this->getHttpClient($this->getGroupXml()),
            $this->getProfileClient(self::EXAMPLE_PROFILES),
            $groupRepo,
            $userRepo,
            new UserFactory(User::class)
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $group = $repo->findByCode('PoPSG');

        $this->assertSame(103582791462551929, $group->getId());
        $this->assertSame('Play or Pay SG', $group->getName());
        $this->assertSame('PoPSG', $group->getCode());
        $this->assertSame('https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/32/3262c28764672bfe03ed9710cefb9cb76ba688ee_full.jpg', $group->getLogoUrl());

        $expectedMembers = [
            [
                'steamid'     => 76561198025823574,
                'personaname' => 'adam1224',
            ],
            [
                'steamid'     => 76561197999563599,
                'personaname' => 'Last.M',
            ],
        ];

        $this->assertCount(count($expectedMembers), $group->getMembers());

        foreach ($group->getMembers() as $position => $member) {
            $expectedMember = $expectedMembers[$position];

            $this->assertSame($expectedMember, [
                'steamid'     => $member->getSteamId(),
                'personaname' => $member->getProfileName(),
            ]);
        }
    }

    private function getEmptyRepo(string $repositoryClass): ServiceEntityRepository
    {
        /** @var ServiceEntityRepository|MockObject $repo */
        $repo = $this->createMock($repositoryClass);
        $repo->method('find')->willReturn(null);

        return $repo;
    }

    /**
     * @param array<int, array<string, int|string>> $expected
     *
     * @return SteamApiClient
     */
    private function getProfileClient(array $expected): SteamApiClient
    {
        /** @var SteamApiClient|MockObject $profileClient */
        $profileClient = $this->createMock(SteamApiClient::class);
        $profileClient->method('loadProfiles')->willReturn($expected);

        return $profileClient;
    }

    /**
     * @param string $willReturn
     *
     * @return ClientInterface
     */
    private function getHttpClient(string $willReturn)
    {
        $groupResponseStream = $this->createMock(StreamInterface::class);
        $groupResponseStream->method('getContents')->willReturn($willReturn);

        $groupResponse = $this->createMock(ResponseInterface::class);
        $groupResponse->method('getBody')->willReturn($groupResponseStream);

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('request')->willReturn($groupResponse);

        return $httpClient;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getGroupXml(): string
    {
        $xml = file_get_contents(__DIR__ . '/../../../fixtures/group.xml');
        if ($xml === false) {
            throw new Exception("Can't load group.xml");
        }

        return $xml;
    }

    const EXAMPLE_PROFILES = [
        [
            'steamid'                  => '76561198025823574',
            'communityvisibilitystate' => 3,
            'profilestate'             => 1,
            'personaname'              => 'adam1224',
            'lastlogoff'               => 1579986322,
            'commentpermission'        => 1,
            'profileurl'               => 'https://steamcommunity.com/id/adam1224/',
            'avatar'                   => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/05/05ebd1c18d1482bff6f8e520c46131760d93d453.jpg',
            'avatarmedium'             => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/05/05ebd1c18d1482bff6f8e520c46131760d93d453_medium.jpg',
            'avatarfull'               => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/05/05ebd1c18d1482bff6f8e520c46131760d93d453_full.jpg',
            'personastate'             => 1,
            'realname'                 => 'Adam',
            'primaryclanid'            => '103582791462171152',
            'timecreated'              => 1274610378,
            'personastateflags'        => 0,
            'gameextrainfo'            => 'Red Johnson\'s Chronicles - 1+2 - Steam Special Edition',
            'gameid'                   => '312050',
        ],
        [
            'steamid'                  => '76561197999563599',
            'communityvisibilitystate' => 3,
            'profilestate'             => 1,
            'personaname'              => 'Last.M',
            'lastlogoff'               => 1579977763,
            'commentpermission'        => 1,
            'profileurl'               => 'https://steamcommunity.com/id/LastM/',
            'avatar'                   => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/02/028eec10b3ad76bc7efc27b1fc74c561b3ef5a5c.jpg',
            'avatarmedium'             => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/02/028eec10b3ad76bc7efc27b1fc74c561b3ef5a5c_medium.jpg',
            'avatarfull'               => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/02/028eec10b3ad76bc7efc27b1fc74c561b3ef5a5c_full.jpg',
            'personastate'             => 0,
            'realname'                 => 'Augusto',
            'primaryclanid'            => '103582791462551929',
            'timecreated'              => 1214698585,
            'personastateflags'        => 0,
            'loccountrycode'           => 'BR',
            'locstatecode'             => '21',
            'loccityid'                => 8334,
        ],
    ];
}

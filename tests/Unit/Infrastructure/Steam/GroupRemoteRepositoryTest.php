<?php

namespace PlayOrPay\Tests\Unit\Infrastructure\Steam;

use PlayOrPay\Domain\User\User;
use PlayOrPay\Infrastructure\Storage\Steam\GroupRemoteRepository;
use PlayOrPay\Infrastructure\Storage\Steam\GroupRepository;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Knojector\SteamAuthenticationBundle\Exception\InvalidApiResponseException;
use Knojector\SteamAuthenticationBundle\Exception\InvalidUserClassException;
use Knojector\SteamAuthenticationBundle\Factory\UserFactory;
use Knojector\SteamAuthenticationBundle\Http\SteamApiClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GroupRemoteRepositoryTest extends TestCase
{
    /**
     * @test
     * @group unit
     * @throws GuzzleException
     * @throws InvalidApiResponseException
     * @throws InvalidUserClassException
     */
    public function should_return_expected_group()
    {
        /** @var GroupRepository $groupRepo */
        $groupRepo = $this->getEmptyRepo(GroupRepository::class);

        /** @var UserRepository $userRepo */
        $userRepo = $this->getEmptyRepo(UserRepository::class);

        $repo = new GroupRemoteRepository(
            $this->getHttpClient(self::EXAMPLE_XML),
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
                'steamid' => 76561198025823574,
                'personaname' => 'adam1224',
            ],
            [
                'steamid' => 76561197999563599,
                'personaname' => 'Last.M',
            ],
        ];

        $this->assertSame(count($expectedMembers), count($group->getMembers()));

        foreach ($group->getMembers() as $position => $member) {
            $expectedMember = $expectedMembers[$position];

            $this->assertSame($expectedMember, [
                'steamid' => $member->getSteamId(),
                'personaname' => $member->getProfileName(),
            ]);
        }
    }

    private function getEmptyRepo(string $repositoryClass)
    {
        $repo = $this->createMock($repositoryClass);
        $repo->method('find')->willReturn(null);
        return $repo;
    }

    /**
     * @param array $expected
     * @return SteamApiClient
     */
    private function getProfileClient(array $expected)
    {
        $profileClient = $this->createMock(SteamApiClient::class);
        $profileClient->method('loadProfiles')->willReturn($expected);
        return $profileClient;
    }

    /**
     * @param string $willReturn
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

    const EXAMPLE_PROFILES = [
        [
            'steamid' => '76561198025823574',
            'communityvisibilitystate' => 3,
            'profilestate' => 1,
            'personaname' => 'adam1224',
            'lastlogoff' => 1579986322,
            'commentpermission' => 1,
            'profileurl' => 'https://steamcommunity.com/id/adam1224/',
            'avatar' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/05/05ebd1c18d1482bff6f8e520c46131760d93d453.jpg',
            'avatarmedium' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/05/05ebd1c18d1482bff6f8e520c46131760d93d453_medium.jpg',
            'avatarfull' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/05/05ebd1c18d1482bff6f8e520c46131760d93d453_full.jpg',
            'personastate' => 1,
            'realname' => 'Adam',
            'primaryclanid' => '103582791462171152',
            'timecreated' => 1274610378,
            'personastateflags' => 0,
            'gameextrainfo' => 'Red Johnson\'s Chronicles - 1+2 - Steam Special Edition',
            'gameid' => '312050',
        ],
        [
            'steamid' => '76561197999563599',
            'communityvisibilitystate' => 3,
            'profilestate' => 1,
            'personaname' => 'Last.M',
            'lastlogoff' => 1579977763,
            'commentpermission' => 1,
            'profileurl' => 'https://steamcommunity.com/id/LastM/',
            'avatar' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/02/028eec10b3ad76bc7efc27b1fc74c561b3ef5a5c.jpg',
            'avatarmedium' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/02/028eec10b3ad76bc7efc27b1fc74c561b3ef5a5c_medium.jpg',
            'avatarfull' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/02/028eec10b3ad76bc7efc27b1fc74c561b3ef5a5c_full.jpg',
            'personastate' => 0,
            'realname' => 'Augusto',
            'primaryclanid' => '103582791462551929',
            'timecreated' => 1214698585,
            'personastateflags' => 0,
            'loccountrycode' => 'BR',
            'locstatecode' => '21',
            'loccityid' => 8334,
        ],
    ];

    const EXAMPLE_XML = <<< XML_CONTENT
<?xml version="1.0" encoding="UTF-8" standalone="yes"?><memberList>
<groupID64>103582791462551929</groupID64>
<groupDetails>
		<groupName><![CDATA[Play or Pay SG]]></groupName>
		<groupURL><![CDATA[PoPSG]]></groupURL>
		<headline><![CDATA[]]></headline>
		<summary><![CDATA[Play or Pay is an event that started years ago inside 2 groups: Beardtopia and The Quips. It used to be run by an awesome person called Erdbeertoertchen, but after they decided to quit, the event stopped happening.<br><br>Since all of us loved it, we decided to bring it back!!!<br><br><a class="bb_link" href="https://steamcommunity.com/linkfilter/?url=https://docs.google.com/document/d/1U5j5ImCXjxaucSdk_BqLTRI21f_4GsDsCbMVoRjsVMY/" target="_blank" rel="noreferrer" >Here</a><span class="bb_link_host">[docs.google.com]</span> are the rules to join our little group/game.]]></summary>
		<avatarIcon><![CDATA[https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/32/3262c28764672bfe03ed9710cefb9cb76ba688ee.jpg]]></avatarIcon>
		<avatarMedium><![CDATA[https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/32/3262c28764672bfe03ed9710cefb9cb76ba688ee_medium.jpg]]></avatarMedium>
		<avatarFull><![CDATA[https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/32/3262c28764672bfe03ed9710cefb9cb76ba688ee_full.jpg]]></avatarFull>
		<memberCount>2</memberCount>
		<membersInChat>0</membersInChat>
		<membersInGame>0</membersInGame>
		<membersOnline>0</membersOnline>
</groupDetails>
<memberCount>2</memberCount>
<totalPages>1</totalPages>
<currentPage>1</currentPage>
<startingMember>0</startingMember>
<members>
<steamID64>76561197999563599</steamID64>
<steamID64>76561198025823574</steamID64>
</members>
</memberList>
XML_CONTENT;
}

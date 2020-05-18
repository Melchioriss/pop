<?php

namespace PlayOrPay\Tests\Unit\Infrastructure\Steam;

use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use PlayOrPay\Infrastructure\Storage\Steam\GroupRemoteRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GroupRemoteRepositoryTest extends TestCase
{
    /**
     * @test
     * @group unit
     *
     * @throws GuzzleException
     * @throws Exception
     */
    public function should_return_expected_group(): void
    {
        $repo = new GroupRemoteRepository($this->getHttpClient($this->getGroupXml()));

        $group = $repo->getByCode('PoPSG');

        $this->assertSame(103582791462551929, $group->id);
        $this->assertSame('Play or Pay SG', $group->name);
        $this->assertSame('PoPSG', $group->code);
        $this->assertSame('https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/32/3262c28764672bfe03ed9710cefb9cb76ba688ee_full.jpg', $group->logoUrl);

        $expectedMembers = [76561197999563599, 76561198025823574];

        $this->assertCount(count($expectedMembers), $group->members);

        foreach ($group->members as $position => $member) {
            $expectedMember = $expectedMembers[$position];

            $this->assertSame($expectedMember, $member);
        }
    }

    /**
     * @param string $willReturn
     *
     * @return ClientInterface
     */
    private function getHttpClient(string $willReturn): ClientInterface
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
     * @throws Exception
     *
     * @return string
     */
    private function getGroupXml(): string
    {
        $xml = file_get_contents(__DIR__ . '/../../../fixtures/group.xml');
        if ($xml === false) {
            throw new Exception("Can't load group.xml");
        }

        return $xml;
    }
}

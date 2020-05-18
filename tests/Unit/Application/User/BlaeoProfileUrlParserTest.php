<?php

namespace PlayOrPay\Tests\Unit\Application\User;

use PHPUnit\Framework\TestCase;
use PlayOrPay\Application\Command\User\User\SetBlaeoName\BlaeoProfileUrlParser;

class BlaeoProfileUrlParserTest extends TestCase
{
    /**
     * @return string[][]
     */
    public function getCorrectProfileUrlCases(): array
    {
        return [
            ['https://www.backlog-assassins.net/users/insideone', 'insideone'],
            ['https://www.backlog-assassins.net/users/insideone/games/beaten', 'insideone'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getIncorrectProfileUrlCases(): array
    {
        return [
            ['https://www.blaeo.net/users/insideone'],
            ['https://www.backlog-assassins.net/abc'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider getCorrectProfileUrlCases
     *
     * @param string $url
     * @param string $expectedName
     */
    public function should_parse_name_from_correct_profile_url(string $url, string $expectedName): void
    {
        $parser = new BlaeoProfileUrlParser();
        $parsedName = $parser->parse($url);

        $this->assertSame($expectedName, $parsedName);
    }

    /**
     * @test
     *
     * @dataProvider getIncorrectProfileUrlCases
     *
     * @param string $url
     */
    public function should_not_parse_from_incorrect_profile_url(string $url): void
    {
        $parser = new BlaeoProfileUrlParser();
        $parsedName = $parser->parse($url);

        $this->assertNull($parsedName);
    }
}

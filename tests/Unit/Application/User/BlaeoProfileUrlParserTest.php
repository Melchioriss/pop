<?php

namespace PlayOrPay\Tests\Unit\Application;

use PHPUnit\Framework\TestCase;
use PlayOrPay\Application\Command\User\User\SetBlaeoName\BlaeoProfileUrlParser;

class BlaeoProfileUrlParserTest extends TestCase
{
    public function getCorrectProfileUrlCases()
    {
        return [
            ['https://www.backlog-assassins.net/users/insideone', 'insideone'],
            ['https://www.backlog-assassins.net/users/insideone/games/beaten', 'insideone'],
        ];
    }

    public function getIncorrectProfileUrlCases()
    {
        return [
            ['https://www.blaeo.net/users/insideone'],
            ['https://www.backlog-assassins.net/abc'],
        ];
    }

    /**
     * @test
     * @dataProvider getCorrectProfileUrlCases
     * @param string $url
     * @param string $expectedName
     */
    public function should_parse_name_from_correct_profile_url(string $url, string $expectedName)
    {
        $parser = new BlaeoProfileUrlParser();
        $parsedName = $parser->parse($url);

        $this->assertSame($expectedName, $parsedName);
    }

    /**
     * @test
     * @dataProvider getIncorrectProfileUrlCases
     * @param string $url
     */
    public function should_not_parse_from_incorrect_profile_url(string $url)
    {
        $parser = new BlaeoProfileUrlParser();
        $parsedName = $parser->parse($url);

        $this->assertSame(null, $parsedName);
    }
}

<?php

namespace PlayOrPay\Application\Command\User\User\SetBlaeoName;

class BlaeoProfileUrlParser
{
    const EXPECTED_HOST = 'www.backlog-assassins.net';

    public function parse(string $profileUrl): ?string
    {
        if (!filter_var($profileUrl, FILTER_VALIDATE_URL)) {
            return $profileUrl;
        }

        $url = parse_url($profileUrl);

        if ($url['host'] !== self::EXPECTED_HOST) {
            return null;
        }

        if (!preg_match('~/users/([^/]+)~', $url['path'], $m)) {
            return null;
        }

        return $m[1];
    }
}

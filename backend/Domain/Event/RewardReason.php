<?php

namespace PlayOrPay\Domain\Event;

use Ducks\Component\SplTypes\SplEnum;

class RewardReason extends SplEnum
{
    const SHORT_GAME_BEATEN = 100;

    const MEDIUM_GAME_BEATEN = 200;

    const LONG_GAME_BEATEN = 300;

    const VERY_LONG_GAME_BEATEN = 400;

    const GAME_COMPLETED = 500;

    const BLAEO_GAMES = 600;

    const ALL_PICKS_BEATEN = 700;
}

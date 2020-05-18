<?php

namespace PlayOrPay\Domain\Game;

use Insideone\Package\EnumFramework\Enum;

class StoreId extends Enum
{
    const STEAM = 10;
    const EPIC_GAMES_STORE = 20;
    const UPLAY = 30;
    const PSN = 40;
    const GOG = 50;
    const XBOX = 60;
    const NINTENDO = 70;
    const GOOGLE_PLAY = 80;
    const APPSTORE = 90;
    const ORIGIN = 100;
}

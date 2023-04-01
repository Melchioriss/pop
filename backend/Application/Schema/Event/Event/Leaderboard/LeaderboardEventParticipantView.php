<?php

namespace PlayOrPay\Application\Schema\Event\Event\Leaderboard;

class LeaderboardEventParticipantView
{
    /** @var LeaderboardEventPickView[] */
    public $picks;

    /** @var float */
    public $blaeoPoints;
}

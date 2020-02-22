<?php

namespace PlayOrPay\Application\Schema\Event\Event\Leaderboard;

class LeaderboardEventPickView
{
    /** @var string */
    public $gameName;

    /** @var LeaderboardEventPickPlayingStateView */
    public $playingState;

    /** @var int */
    public $achievements;

    /** @var int */
    public $type;

    /** @var int */
    public $pickerType;
}

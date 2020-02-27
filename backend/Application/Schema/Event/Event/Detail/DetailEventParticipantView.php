<?php

namespace PlayOrPay\Application\Schema\Event\Event\Detail;

use Ramsey\Uuid\UuidInterface;

class DetailEventParticipantView
{
    /** @var UuidInterface */
    public $uuid;

    /** @var int */
    public $user;

    /** @var bool */
    public $active;

    /** @var string */
    public $groupWins;

    /** @var string */
    public $blaeoGames;

    /** @var string */
    public $extraRules;

    /** @var DetailEventPickerView[] */
    public $pickers;

    /** @var DetailEventEarnedReward[] */
    public $rewards;

    /** @var int */
    public $totalRewardValue;
}

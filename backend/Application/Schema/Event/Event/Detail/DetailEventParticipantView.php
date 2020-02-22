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

    /** @var int */
    public $blaeoPoints;

    /** @var string */
    public $extraRules;

    /** @var DetailEventPickerView[] */
    public $pickers;
}

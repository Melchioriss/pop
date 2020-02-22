<?php

namespace PlayOrPay\Application\Command\User\User\SetBlaeoName;

use Exception;
use PlayOrPay\Domain\Steam\SteamId;

class SetUserBlaeoNameCommand
{
    /** @var SteamId */
    private $steamId;

    /** @var string */
    private $blaeoName;

    /**
     * @param int    $steamId
     * @param string $blaeoName
     *
     * @throws Exception
     */
    public function __construct(int $steamId, string $blaeoName)
    {
        $this->steamId = new SteamId($steamId);

        if ($blaeoName) {
            $blaeoParsedName = (new BlaeoProfileUrlParser())->parse($blaeoName);
            if ($blaeoParsedName === null) {
                throw new Exception("Blaeo name couldn't be parsed");
            }
            $this->blaeoName = $blaeoParsedName;
        } else {
            $this->blaeoName = $blaeoName;
        }
    }

    public function getSteamId(): SteamId
    {
        return $this->steamId;
    }

    public function getBlaeoName(): string
    {
        return $this->blaeoName;
    }
}

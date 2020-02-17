<?php

namespace PlayOrPay\Infrastructure\Storage\User;

use PlayOrPay\Domain\User\User;

interface ActorFinderInterface
{
    public function findActor(): ?User;
}

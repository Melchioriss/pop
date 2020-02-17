<?php

namespace PlayOrPay\Domain\Contracts\Entity;

interface OnUpdateEventListenerInterface
{
    function onUpdate(): void;
}

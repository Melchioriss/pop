<?php

namespace PlayOrPay\Domain\Contracts\Entity;

interface OnUpdateEventListenerInterface
{
    public function onUpdate(): void;
}

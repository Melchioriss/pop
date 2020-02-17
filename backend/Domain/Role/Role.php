<?php

namespace PlayOrPay\Domain\Role;

use Doctrine\Common\Collections\ArrayCollection;

class Role
{
    /** @var RoleName */
    private $name;

    /** @var string[] */
    private $abilities;

    public function __construct(RoleName $name, $abilities = [])
    {
        $this->name = $name;
        $this->abilities = new ArrayCollection;
        $this->addAbilities($abilities);
    }

    public function addAbility(string $ability): self
    {
        if ($this->abilities->contains($ability)) {
            return $this;
        }

        $this->abilities->add($ability);
        return $this;
    }

    private function addAbilities(array $abilities): self
    {
        foreach ($abilities as $ability) {
            $this->addAbility($ability);
        }

        return $this;
    }

    public function getName(): RoleName
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return (string)$this->getName();
    }
}

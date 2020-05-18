<?php

namespace PlayOrPay\Domain\Role;

use Doctrine\Common\Collections\ArrayCollection;
use PlayOrPay\Domain\Contracts\Entity\AggregateInterface;
use PlayOrPay\Domain\Contracts\Entity\AggregateTrait;

class Role implements AggregateInterface
{
    use AggregateTrait;

    /** @var RoleName */
    private $name;

    /** @var string[]|ArrayCollection<int, string> */
    private $abilities;

    /**
     * @param RoleName $name
     * @param string[] $abilities
     */
    public function __construct(RoleName $name, $abilities = [])
    {
        $this->name = $name;
        $this->abilities = new ArrayCollection();
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

    /**
     * @param string[] $abilities
     *
     * @return Role
     */
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
        return (string) $this->getName();
    }
}

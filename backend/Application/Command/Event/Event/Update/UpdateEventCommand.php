<?php

namespace PlayOrPay\Application\Command\Event\Event\Update;

use Assert\Assert;
use DateTime;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UpdateEventCommand
{
    const DATE_FORMAT = 'Y-m-d';

    /** @var UuidInterface */
    private $uuid;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var DateTime */
    private $startingOn;

    /** @var DateTime */
    private $endingOn;

    /**
     * @param string $uuid
     * @param string $name
     * @param string $description
     * @param string $startingOn
     * @param string $endingOn
     *
     * @throws Exception
     */
    public function __construct(string $uuid, string $name, string $description, string $startingOn, string $endingOn)
    {
        Assert::lazy()
            ->that($uuid)->uuid()
            ->that($name)->minLength(3, 'The title is too short')
            ->that($startingOn)->date(self::DATE_FORMAT, 'Starting date has a wrong format')
            ->that($endingOn)->date(self::DATE_FORMAT, 'Ending date has a wrong format')
            ->verifyNow();

        $this->uuid = Uuid::fromString($uuid);
        $this->name = $name;
        $this->description = $description;
        $this->startingOn = new DateTime($startingOn);
        $this->endingOn = new DateTime($endingOn);
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStartingOn(): DateTime
    {
        return $this->startingOn;
    }

    public function getEndingOn(): DateTime
    {
        return $this->endingOn;
    }
}

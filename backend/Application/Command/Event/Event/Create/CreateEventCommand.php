<?php

namespace PlayOrPay\Application\Command\Event\Event\Create;

use Assert\Assert;
use DateTime;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CreateEventCommand
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

    /** @var string */
    private $group;

    /**
     * @param string $uuid
     * @param string $name
     * @param string $description
     * @param string $startingOn
     * @param string $endingOn
     * @param string $group
     *
     * @throws Exception
     */
    public function __construct(string $uuid, string $name, string $description, string $startingOn, string $endingOn, string $group)
    {
        Assert::lazy()
            ->that($uuid)->uuid()
            ->that($name)->minLength(3, 'The title should contain at least 3 symbols')
            ->that($startingOn)->date(self::DATE_FORMAT, 'Starting date should be in format YYYY-mm-dd')
            ->that($endingOn)->date(self::DATE_FORMAT, 'Ending date should be in format YYYY-mm-dd')
            ->verifyNow();

        $this->uuid = Uuid::fromString($uuid);
        $this->name = $name;
        $this->description = $description;
        $this->startingOn = new DateTime($startingOn);
        $this->endingOn = new DateTime($endingOn);
        $this->group = $group;
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

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getGroup(): string
    {
        return $this->group;
    }
}

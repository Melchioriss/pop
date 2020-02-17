<?php

declare(strict_types=1);

namespace PlayOrPay\UI\Http\Rest\Controller;

use PlayOrPay\UI\Http\Rest\Response\JsonApiFormatter;
use League\Tactician\CommandBus;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CommandQueryController extends QueryController
{
    protected function exec($command): void
    {
        $this->commandBus->handle($command);
    }

    public function __construct(CommandBus $commandBus, CommandBus $queryBus, JsonApiFormatter $formatter, UrlGeneratorInterface $router, CommandBus $securityBus)
    {
        parent::__construct($queryBus, $formatter, $router, $securityBus);
        $this->commandBus = $commandBus;
    }

    /**
     * @var CommandBus
     */
    private $commandBus;
}

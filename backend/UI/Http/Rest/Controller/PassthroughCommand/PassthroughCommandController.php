<?php

namespace PlayOrPay\UI\Http\Rest\Controller\PassthroughCommand;

use League\Tactician\CommandBus;
use PlayOrPay\Security\SecuriryException;
use PlayOrPay\UI\Http\Rest\Controller\CommandQueryController;
use PlayOrPay\UI\Http\Rest\Controller\PassthroughQuery\RequiredParameterNotFound;
use PlayOrPay\UI\Http\Rest\RequestConverter\RequestConverter;
use PlayOrPay\UI\Http\Rest\Response\JsonApiFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PassthroughCommandController extends CommandQueryController
{
    /** @var RequestConverter */
    private $requestConverter;

    public function __construct(
        CommandBus $commandBus,
        CommandBus $queryBus,
        JsonApiFormatter $formatter,
        UrlGeneratorInterface $router,
        CommandBus $securityBus,
        RequestConverter $requestConverter
    ) {
        parent::__construct($commandBus, $queryBus, $formatter, $router, $securityBus);
        $this->requestConverter = $requestConverter;
    }

    /**
     * @param Request $request
     * @param string  $command
     *
     * @throws RequiredParameterNotFound
     * @throws SecuriryException
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request, string $command): JsonResponse
    {
        $commandInstance = $this->requestConverter->convert($request, $command);
        $this->confirm($commandInstance);
        $this->exec($commandInstance);

        return JsonResponse::create();
    }
}

<?php

namespace PlayOrPay\UI\Http\Rest\Controller\PassthroughQuery;

use League\Tactician\CommandBus;
use PlayOrPay\Application\Query\Collection;
use PlayOrPay\Domain\Exception\NotFoundException;
use PlayOrPay\Security\SecuriryException;
use PlayOrPay\UI\Http\Rest\Controller\QueryController;
use PlayOrPay\UI\Http\Rest\RequestConverter\RequestConverter;
use PlayOrPay\UI\Http\Rest\Response\JsonApiFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PassthroughQueryController extends QueryController
{
    /** @var RequestConverter */
    private $requestConverter;

    public function __construct(
        CommandBus $queryBus,
        JsonApiFormatter $formatter,
        UrlGeneratorInterface $router,
        CommandBus $securityBus,
        RequestConverter $requestConverter
    ) {
        parent::__construct($queryBus, $formatter, $router, $securityBus);
        $this->requestConverter = $requestConverter;
    }

    /**
     * @param Request $request
     * @param string  $query
     *
     * @throws RequiredParameterNotFound
     * @throws NotFoundException
     * @throws SecuriryException
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request, string $query): JsonResponse
    {
        $queryInstance = $this->requestConverter->convert($request, $query);
        $this->confirm($queryInstance);
        $response = $this->ask($queryInstance);

        if (is_array($response)) {
            $size = count($response);
            $collection = new Collection(1, $size, $size, $response);

            return $this->json($collection);
        }// elseif (!$response instanceof Collection) {
        //    throw IncompatibleHandlerException::becauseItShouldReturnCollectionOrArray(self::class, $query, $response);
        //}

        return $this->json($response);
    }
}

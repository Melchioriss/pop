<?php

declare(strict_types=1);

namespace PlayOrPay\UI\Http\Rest\Controller;

use League\Tactician\CommandBus;
use League\Tactician\Exception\MissingHandlerException;
use PlayOrPay\Application\Query\Collection;
use PlayOrPay\Security\SecuriryException;
use PlayOrPay\UI\Http\Rest\Response\JsonApiFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class QueryController
{
    private const CACHE_MAX_AGE = 31536000; // Year.

    /**
     * @param object $action
     *
     * @throws SecuriryException
     */
    protected function confirm(object $action): void
    {
        try {
            $this->securityBus->handle($action);
        } catch (MissingHandlerException $e) {
            throw SecuriryException::becauseYouMustImplementSecurityHandler(get_class($action));
        }
    }

    /**
     * @param object $query
     *
     * @return mixed
     */
    protected function ask(object $query)
    {
        return $this->queryBus->handle($query);
    }

    protected function jsonCollection(Collection $collection, bool $isImmutable = false): JsonResponse
    {
        $response = $this->createResponse($collection);

        $this->decorateWithCache($response, $collection, $isImmutable);

        return $response;
    }

    protected function json(?object $resource): JsonResponse
    {
        return $this->createResponse($resource);
    }

    /**
     * @param object|Collection|null $data
     * @param int $status
     * @param array<string, string> $headers
     *
     * @return JsonResponse
     */
    private function createResponse($data, int $status = JsonResponse::HTTP_OK, $headers = []): JsonResponse
    {
        return new JsonResponse(
            $data instanceof Collection
                ? $this->formatter->collection($data)
                : $this->formatter->one($data),
            $status,
            $headers,
            true
        );
    }

    /**
     * @param string $name
     * @param array<string, string|int|float> $params
     *
     * @return string
     */
    protected function route(string $name, array $params = []): string
    {
        return $this->router->generate($name, $params);
    }

    private function decorateWithCache(JsonResponse $response, Collection $collection, bool $isImmutable): void
    {
        if ($isImmutable && $collection->limit === count($collection->data)) {
            $response
                ->setMaxAge(self::CACHE_MAX_AGE)
                ->setSharedMaxAge(self::CACHE_MAX_AGE);
        }
    }

    public function __construct(CommandBus $queryBus, JsonApiFormatter $formatter, UrlGeneratorInterface $router, CommandBus $securityBus)
    {
        $this->queryBus = $queryBus;
        $this->formatter = $formatter;
        $this->router = $router;
        $this->securityBus = $securityBus;
    }

    /**
     * @var JsonApiFormatter
     */
    private $formatter;

    /**
     * @var CommandBus
     */
    private $queryBus;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /** @var CommandBus */
    private $securityBus;
}

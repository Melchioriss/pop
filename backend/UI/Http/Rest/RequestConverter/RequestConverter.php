<?php

namespace PlayOrPay\UI\Http\Rest\RequestConverter;

use Assert\Assert;
use PlayOrPay\UI\Http\Rest\Controller\PassthroughQuery\RequiredParameterNotFound;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

class RequestConverter
{
    /**
     * @param Request $request
     * @param string  $targetClass
     *
     * @throws RequiredParameterNotFound
     *
     * @return object
     */
    public function convert(Request $request, string $targetClass): object
    {
        Assert::that($targetClass)->classExists();

        $constructParameters = $this->getConstructParameters($request, $targetClass);

        return new $targetClass(...$constructParameters);
    }

    /** @noinspection PhpDocMissingThrowsInspection */

    /**
     * @param Request $request
     * @param string  $targetClass
     *
     * @throws RequiredParameterNotFound
     *
     * @return array<int, string|int|bool|null>
     */
    private function getConstructParameters(Request $request, string $targetClass): array
    {
        /** @noinspection PhpUnhandledExceptionInspection See ExceptionSubscriber */
        $classConstructor = (new ReflectionClass($targetClass))->getConstructor();

        if ($classConstructor === null) {
            return [];
        }

        $constructParameters = [];
        foreach ($classConstructor->getParameters() as $parameterDefinition) {
            $parameterName = $parameterDefinition->getName();

            $constructParameter = $request->get($parameterName);
            if ($constructParameter === null) {
                if ($parameterDefinition->isOptional()) {
                    /** @noinspection PhpUnhandledExceptionInspection We've checked it in the line above */
                    $constructParameter = $parameterDefinition->getDefaultValue();
                } elseif (!$parameterDefinition->allowsNull()) {
                    throw new RequiredParameterNotFound($parameterName);
                }
            }

            $constructParameters[] = $constructParameter;
        }

        return $constructParameters;
    }
}

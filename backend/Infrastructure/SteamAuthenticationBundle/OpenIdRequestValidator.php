<?php

namespace PlayOrPay\Infrastructure\SteamAuthenticationBundle;

use Knojector\SteamAuthenticationBundle\Security\Authentication\Validator\RequestValidator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OpenIdRequestValidator extends RequestValidator
{
    protected function validateReturnTo(): bool
    {
        if (!$this->request->query->has('openid_return_to')) {
            return false;
        }

        $requestedReturnTo = $this->request->query->get('openid_return_to');
        $knownReturnTo = $this->router->generate($this->loginRoute, [], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->getUrlWithoutSchema($requestedReturnTo) === $this->getUrlWithoutSchema($knownReturnTo);
    }

    protected function getUrlWithoutSchema(string $url): string
    {
        return preg_replace('~^(http[s]?)~', '', $url);
    }
}

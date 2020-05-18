<?php

namespace PlayOrPay\UI\Http\Rest\Controller\Logout;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LogoutController
{
    /**
     * @Route("/logout")
     *
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $tokenStorage->setToken(null);

        return new JsonResponse([]);
    }
}

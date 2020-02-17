<?php

namespace PlayOrPay\UI\Http\Rest\Controller;

use Symfony\Component\HttpFoundation\Response;

class StaticController
{
    public function __invoke(string $template): Response
    {
        return new Response(file_get_contents($template));
    }
}

<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Http\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Twig\Environment;

class CustomErrorController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function show(FlattenException $exception): Response
    {
        $statusCode = $exception->getStatusCode();
        $statusText = Response::$statusTexts[$statusCode] ?? 'Erreur';

        $template = $statusCode === 404
            ? 'bundles/TwigBundle/Exception/error404.html.twig'
            : 'bundles/TwigBundle/Exception/error.html.twig';

        return new Response(
            $this->twig->render($template, [
                'status_code' => $statusCode,
                'status_text' => $statusText,
                'exception' => $exception,
            ]),
            $statusCode
        );
    }

}

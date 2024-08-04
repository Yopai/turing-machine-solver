<?php

namespace App\Controller;

use App\CardLoader;
use App\DataObject\Code;
use App\Http\HttpResponseInterface;
use App\Http\RedirectResponse;
use App\Http\Request;
use App\Http\Response;
use App\Service\Engine;
use Twig\Environment as TwigEnv;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GenerateController
{
    /**
     * @throws \Exception
     */
    public function __invoke(Engine $engine, Request $request): HttpResponseInterface
    {
        $nightmare = (bool)$request->post('nightmare');
        $count = $request->post('count');
        $wrong = $request->post('wrong');
        echo $count, ' - ', $wrong;
        $problem = match ($wrong) {
            'classic' => $engine->generateByCardProblem($count, 0, $nightmare),
            'extreme' => $engine->generateByCardProblem($count, 1, $nightmare),
            default => $engine->generateExtendedProblem($count, $wrong, $ignored, $nightmare),
        };

        return new RedirectResponse('/play/' . $engine->getProblemHash($problem));
    }
}
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

class SetupController
{
    public function __invoke(Engine $engine, Request $request): HttpResponseInterface
    {
        $nightmare = (bool)$request->post('nightmare');
        $problem = match ($request->post('mode')) {
            'classic' => $engine->setupClassicProblem($request->post('card'), $nightmare),
            'extreme' => $engine->setupExtremeProblem($request->post('bicards'), $nightmare),
        };

        return new RedirectResponse('/play/' . $engine->getProblemHash($problem));
    }
}
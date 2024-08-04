<?php

namespace App\Controller;

use App\CardLoader;
use App\DataObject\Code;
use App\Http\Request;
use App\Http\Response;
use App\Service\Engine;
use Twig\Environment as TwigEnv;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PlayController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __invoke(Engine $engine, TwigEnv $tpl, Request $request, string $hash): Response
    {
        $problem = $engine->loadProblem($hash);
        $symbolsCount = 3; // TODO : store in $problem, if not deductible
        return new Response($tpl->render('sheet.html.twig', [
            'hash' => $hash,
            'problem' => $problem,
            'letters' => self::letterrange(count($problem->getCriteriaGroups())),
            'symbols' => array_slice(Code::SYMBOLS, 0, $symbolsCount),
            'codes' => $engine->getAllCodes(),
            'useGame' => true,
        ]));
    }

    private function letterrange($count): array
    {
        return range('A', chr(ord('A') + $count - 1));
    }
}
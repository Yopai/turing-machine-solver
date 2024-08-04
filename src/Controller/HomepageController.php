<?php

namespace App\Controller;

use App\CardLoader;
use App\Http\Request;
use App\Http\Response;
use Twig\Environment as TwigEnv;

class HomepageController
{
    public function __invoke(CardLoader $loader, TwigEnv $tpl, Request $request): Response
    {
        return new Response($tpl->render('index.html.twig', [
            'cards' => $loader->getAll(),
        ]));
    }
}
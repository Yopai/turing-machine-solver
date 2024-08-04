<?php

namespace App\Controller\Api;

use App\Http\HttpResponseInterface;
use App\Http\JsonResponse;
use App\Service\Engine;
use App\Http\Request;
use function iterator_to_array;

class ApiSolveController
{
    public function __construct(private Engine $engine)
    {
    }

    /**
     * @param Request $request
     * @return HttpResponseInterface
     */
    public function __invoke(Request $request): HttpResponseInterface
    {
        // TODO : use hash and problem instead of cards
        $cards = $request->post('card');
        foreach ($cards as &$card) {
            if (is_array($card)) {
                $card = implode('+', array_filter($card));
            }
            $card = preg_replace('/[^+0-9]/', '', $card);
        }
        $cards = array_filter($cards);
        $this->engine->init($cards);
        $solutions = iterator_to_array($this->engine->findPossible());
        $result = (object)[
            'count' => count($solutions),
            'solutions' => [],
        ];
        foreach ($solutions as $solution) {
            $result->solutions [] = (object)[
                'criteria' => array_map(function ($criterion) {
                    return $criterion->__toString();
                }, $solution->criteria),
                'code' => $solution->code->__toString(),
            ];
        }
        return new JsonResponse($result);
    }
}
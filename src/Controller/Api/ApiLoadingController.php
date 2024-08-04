<?php

namespace App\Controller\Api;

use App\Http\HttpResponseInterface;
use App\Http\JsonResponse;
use App\Service\Engine;
use App\Http\Request;

class ApiLoadingController
{
    public function __construct(private Engine $engine)
    {
    }

    /**
     * @param Request $request
     * @param string $hash
     * @return HttpResponseInterface
     */
    public function __invoke(Request $request, string $hash): HttpResponseInterface
    {
        $problem = $this->engine->loadProblem($hash);

        $response = (object)[
            'groups' => [],
            'nightmare' => $problem->isNightmare(),
            'answers' => [],
        ];

        foreach ($problem->getShuffledCriteriaGroups() as $criteriaGroup) {
            $groupids = array_map(fn($criterion) => $criterion->getId(), $criteriaGroup);
            sort($groupids);
            $group = (object)[
                'id' => implode('', $groupids),
                'criteria' => [],
            ];
            foreach ($criteriaGroup as $criterion) {
                $group->criteria[]= (object)[
                    'id' => $criterion->getId(),
                    'label' => $criterion->getLabel(),
                    'prefix' => $criterion->getCard(),
                ];
            }
            $response->groups[]= $group;
        }

        return new JsonResponse($response);
    }
}
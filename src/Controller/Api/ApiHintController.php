<?php

namespace App\Controller\Api;

use App\DataObject\Code;
use App\Exception\PartialCodeException;
use App\Http\HttpResponseInterface;
use App\Http\JsonResponse;
use App\Service\Engine;
use App\Http\Request;

class ApiHintController
{
    public function __construct(private Engine $engine)
    {
    }

    /**
     * @param Request $request
     * @param string $hash
     * @param string $code
     * @return HttpResponseInterface
     * @throws PartialCodeException
     */
    public function __invoke(Request $request, string $hash, string $code): HttpResponseInterface
    {
        $problem = $this->engine->loadProblem($hash);
        if ($code === 'help') {
            header('Content-Type: text/plain');
            foreach ($this->engine->getAllCodes() as $code) {
                echo "$code: ";
                foreach ($problem->getCriteriaGroups() as $group) {
                    $n = 0;
                    foreach ($group as $criterion) {
                        if ($criterion($code)) {
                            $n++;
                        }
                    }
                    echo "$n/".count($group).'='.number_format($n/count($group), 1).'   ';
                }
                echo "\n";
            }
            exit;
        }
        $code = new Code(str_split($code));

        $response = (object)[
            'answers' => [],
        ];
        foreach ($problem->getCriteriaGroups() as $group) {
            foreach ($group as $criterion) {
                try {
                    $response->answers[] = (object)[
                        'id' => $criterion->getId(),
                        'answer' => $criterion($code),
                    ];
                } catch (PartialCodeException) {
                    // do nothing
                }
            }
        }
        usort($response->answers, fn($a, $b) => $a->id <=> $b->id);
        return new JsonResponse($response);
    }
}
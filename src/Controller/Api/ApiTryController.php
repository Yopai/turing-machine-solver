<?php

namespace App\Controller\Api;

use App\DataObject\Code;
use App\Http\HttpResponseInterface;
use App\Http\JsonResponse;
use App\Service\Engine;
use App\Http\Request;

class ApiTryController
{
    public function __construct(private Engine $engine)
    {
    }

    /**
     * @param Request $request
     * @param string $hash
     * @param string $code
     * @param string $letter
     * @return HttpResponseInterface
     */
    public function __invoke(Request $request, string $hash, string $code, string $letter): HttpResponseInterface
    {
        $problem = $this->engine->loadProblem($hash);

        $response = (object)[
            'code' => $code,
            'letter' => $letter,
            'answer' => $problem->getAnswer($letter, new Code(str_split($code))),
        ];

        return new JsonResponse($response);
    }
}
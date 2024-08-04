<?php

namespace App\Controller\Api;

use App\DataObject\Code;
use App\Exception\PartialCodeException;
use App\Http\HttpResponseInterface;
use App\Http\JsonResponse;
use App\Service\Engine;
use App\Http\Request;

class ApiCheckController
{
    public function __construct(private Engine $engine)
    {
    }

    /**
     * @param Request $request
     * @param string $hash
     * @param string $code
     * @return HttpResponseInterface
     */
    public function __invoke(Request $request, string $hash, string $code): HttpResponseInterface
    {
        $problem = $this->engine->loadProblem($hash);
        return new JsonResponse($problem->check(new Code(str_split($code))));
    }
}
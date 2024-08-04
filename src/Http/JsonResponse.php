<?php

namespace App\Http;

class JsonResponse implements HttpResponseInterface
{

    /**
     * @param object|array|scalar $response
     */
    public function __construct(private mixed $response)
    {
    }

    public function send()
    {
        header('Content-Type: application/json');
        echo json_encode($this->response);
    }
}
<?php

namespace App\Http;

class Response implements HttpResponseInterface
{

    public function __construct(private string $response)
    {
    }

    public function send()
    {
        echo $this->response;
    }
}

